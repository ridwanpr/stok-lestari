<?php

namespace App\Http\Requests;

use App\Models\Batch;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class FlashSaleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'batch_id' => ['required', 'integer', 'exists:batches,id'],
            'discount_price' => ['required', 'integer', 'min:100'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'batch_id' => 'batch',
            'discount_price' => 'harga diskon',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $batchId = $this->input('batch_id');
            if (! $batchId) {
                return;
            }

            /** @var Batch|null $batch */
            $batch = Batch::with(['product', 'flashSales' => fn ($q) => $q->active()])->find($batchId);
            if (! $batch) {
                return;
            }

            if ($batch->status !== 'active' || $batch->expiry_date->isPast() && ! $batch->expiry_date->isToday()) {
                $validator->errors()->add('batch_id', 'Batch yang dipilih harus berstatus aktif dan belum kedaluwarsa.');
            }

            if ($batch->flashSales->isNotEmpty()) {
                $validator->errors()->add('batch_id', 'Batch yang dipilih sudah memiliki promo flash sale yang sedang aktif.');
            }

            $discountPrice = (int) $this->input('discount_price');
            if ($batch->product && $discountPrice >= $batch->product->normal_price) {
                $validator->errors()->add('discount_price', 'Harga diskon harus lebih rendah dari harga normal produk (Rp '.number_format($batch->product->normal_price, 0, ',', '.').').');
            }
        });
    }
}
