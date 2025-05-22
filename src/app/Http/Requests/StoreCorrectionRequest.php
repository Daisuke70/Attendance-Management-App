<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCorrectionRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i'],
            'note' => ['required'],
            'break_times.*.start_time' => ['nullable', 'date_format:H:i'],
            'break_times.*.end_time' => ['nullable', 'date_format:H:i'],
        ];
    }

    public function messages(): array
    {
        return [
            'start_time.required' => '出勤時間を入力してください',
            'end_time.required' => '退勤時間を入力してください',
            'note.required' => '備考を記入してください',
            'start_time.date_format' => '出勤時間の形式が正しくありません',
            'end_time.date_format' => '退勤時間の形式が正しくありません',
            'break_times.*.start_time.date_format' => '休憩開始時間の形式が正しくありません',
            'break_times.*.end_time.date_format' => '休憩終了時間の形式が正しくありません',
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
    
                    if (($breakStart && ($breakStart < $startTime || $breakStart > $endTime)) ||
                        ($breakEnd && ($breakEnd < $startTime || $breakEnd > $endTime))) {
                        $validator->errors()->add("break_times.{$i}.start_time", '休憩時間が勤務時間外です');
                    }
    
                    if ($breakStart && $breakEnd && $breakStart >= $breakEnd) {
                        $validator->errors()->add("break_times.{$i}.start_time", '休憩開始時間もしくは休憩終了時間が不適切な値です');
                    }

                    if ($breakStart && $breakEnd) {
                        try {
                            $startMinutes = \Carbon\Carbon::parse($breakStart)->hour * 60 + \Carbon\Carbon::parse($breakStart)->minute;
                            $endMinutes = \Carbon\Carbon::parse($breakEnd)->hour * 60 + \Carbon\Carbon::parse($breakEnd)->minute;
    
                            foreach ($ranges as $range) {
                                if (!($endMinutes <= $range['start'] || $startMinutes >= $range['end'])) {
                                    $validator->errors()->add("break_times.$i.start_time", '休憩時間が他の休憩と重複しています');
                                    break;
                                }
                            }
    
                            $ranges[] = ['start' => $startMinutes, 'end' => $endMinutes];
                        } catch (\Exception $e) {

                        }
                    }
                }
            }
        });
    }
}
