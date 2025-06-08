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
    
        
    
            $breakTimes = $this->input('break_times', []);
    
            if ($startTime && $endTime && is_array($breakTimes)) {
                $ranges = [];
    
                foreach ($breakTimes as $i => $break) {
                    $breakStart = $break['start_time'] ?? null;
                    $breakEnd = $break['end_time'] ?? null;

                    if (empty($breakStart) && empty($breakEnd)) {
                        continue;
                    }

                    if (empty($breakStart) || empty($breakEnd)) {
                        $validator->errors()->add("break_times.$i.start_time", '休憩開始時間と休憩終了時間の両方を入力してください。');
                        continue;
                    }

                    if (
                        ($breakStart < $startTime || $breakStart > $endTime) ||
                        ($breakEnd < $startTime || $breakEnd > $endTime)
                    ) {
                        $validator->errors()->add("break_times.$i.start_time", '休憩時間が勤務時間外です。');
                        continue;
                    }

                    if ($breakStart >= $breakEnd) {
                        $validator->errors()->add("break_times.$i.start_time", '休憩開始時間もしくは終了時間が不適切な値です。');
                        continue;
                    }

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

                    }
                }
            }
        });
    }
}
