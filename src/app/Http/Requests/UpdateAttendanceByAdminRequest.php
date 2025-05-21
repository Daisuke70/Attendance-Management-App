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
            $breakTimes = $this->input('break_times', []);
            $ranges = [];
    
            foreach ($breakTimes as $index => $break) {
                $start = $break['start_time'] ?? null;
                $end = $break['end_time'] ?? null;
    
                if (!$start || !$end) {
                    continue;
                }

                if ($start >= $end) {
                    $validator->errors()->add("break_times.$index.start_time", '休憩開始時間もしくは休憩終了時間が不適切な値です。');
                }
    
                try {
                    $startMinutes = \Carbon\Carbon::parse($start)->hour * 60 + \Carbon\Carbon::parse($start)->minute;
                    $endMinutes = \Carbon\Carbon::parse($end)->hour * 60 + \Carbon\Carbon::parse($end)->minute;
    
                    foreach ($ranges as $range) {
                        if (!($endMinutes <= $range['start'] || $startMinutes >= $range['end'])) {
                            $validator->errors()->add("break_times.$index.start_time", '休憩時間が他の休憩と重複しています。');
                            break;
                        }
                    }
    
                    $ranges[] = ['start' => $startMinutes, 'end' => $endMinutes];
    
                } catch (\Exception $e) {

                }
            }
        });
    }
}
