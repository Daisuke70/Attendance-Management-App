<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAttendanceByAdminRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'note' => ['required'],
    
            'break_times.*.start_time' => [
                'nullable',
                'date_format:H:i',
                'after_or_equal:start_time',
                'before_or_equal:end_time',
            ],
            'break_times.*.end_time' => [
                'nullable',
                'date_format:H:i',
                'after_or_equal:start_time',
                'before_or_equal:end_time',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'end_time.after' => '出勤時間もしくは退勤時間が不適切な値です。',
            'break_times.*.start_time.after_or_equal' => '休憩時間が勤務時間外です。',
            'break_times.*.start_time.before_or_equal' => '休憩時間が勤務時間外です。',
            'break_times.*.end_time.after_or_equal' => '休憩時間が勤務時間外です。',
            'break_times.*.end_time.before_or_equal' => '休憩時間が勤務時間外です.',
            'note.required' => '備考を記入してください。',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $startTime = $this->input('start_time');
            $endTime = $this->input('end_time');
    
            if ($startTime && $endTime && $startTime >= $endTime) {
                $validator->errors()->add('start_time', '出勤時間もしくは退勤時間が不適切な値です');
            }
    
            $breakTimes = $this->input('break_times', []);
    
            if ($startTime && $endTime && is_array($breakTimes)) {
                $ranges = [];
    
                foreach ($breakTimes as $i => $break) {
                    $breakStart = $break['start_time'] ?? null;
                    $breakEnd = $break['end_time'] ?? null;
    
                    // 両方空ならスキップ
                    if (empty($breakStart) && empty($breakEnd)) {
                        continue;
                    }
    
                    // 一方だけ空ならエラー
                    if (empty($breakStart) || empty($breakEnd)) {
                        $validator->errors()->add("break_times.$i.start_time", '休憩開始時間と終了時間の両方を入力してください。');
                        continue;
                    }
    
                    // 勤務時間外チェック → start_time のみにメッセージ
                    if (
                        ($breakStart < $startTime || $breakStart > $endTime) ||
                        ($breakEnd < $startTime || $breakEnd > $endTime)
                    ) {
                        $validator->errors()->add("break_times.$i.start_time", '休憩時間が勤務時間外です。');
                        continue;
                    }
    
                    // 開始時間 >= 終了時間チェック
                    if ($breakStart >= $breakEnd) {
                        $validator->errors()->add("break_times.$i.start_time", '休憩開始時間もしくは終了時間が不適切な値です。');
                        continue;
                    }
    
                    // 重複チェック
                    try {
                        $startMinutes = \Carbon\Carbon::parse($breakStart)->hour * 60 + \Carbon\Carbon::parse($breakStart)->minute;
                        $endMinutes = \Carbon\Carbon::parse($breakEnd)->hour * 60 + \Carbon\Carbon::parse($breakEnd)->minute;
    
                        foreach ($ranges as $range) {
                            if (!($endMinutes <= $range['start'] || $startMinutes >= $range['end'])) {
                                $validator->errors()->add("break_times.$i.start_time", '休憩時間が他の休憩と重複しています。');
                                break;
                            }
                        }
    
                        $ranges[] = ['start' => $startMinutes, 'end' => $endMinutes];
    
                    } catch (\Exception $e) {
                        // パースエラーは無視
                    }
                }
            }
        });
    }
}
