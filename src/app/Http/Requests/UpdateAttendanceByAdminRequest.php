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
                'required',
                'date_format:H:i',
                'after_or_equal:start_time',
                'before_or_equal:end_time',
            ],
            'break_times.*.end_time' => [
                'required',
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
}
