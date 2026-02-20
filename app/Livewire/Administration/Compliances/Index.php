<?php

namespace App\Livewire\Administration\Compliances;

use App\Models\ComplianceMaster;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    public $name, $description, $class, $duration_months = 12, $status = 1;
    protected function rules()
{
    return [
        'name'            => 'required|string|min:3|max:255',
        'description'     => 'required|string|min:10',
        'class'           => 'required|string|max:100',
        'duration_months' => 'nullable|integer|min:0|max:120', // Maksimal 10 tahun (120 bulan)
        'status'          => 'required|boolean',
    ];
}

protected function messages()
{
    return [
        'name.required'            => 'Nama kepatuhan wajib diisi.',
        'name.min'                 => 'Nama kepatuhan minimal 3 karakter.',
        'description.required'     => 'Deskripsi wajib diisi agar informasi jelas.',
        'description.min'          => 'Deskripsi terlalu singkat, berikan penjelasan lebih detail.',
        'class.required'           => 'Kategori (Class) wajib dipilih atau diisi.',
        'duration_months.integer'  => 'Durasi harus berupa angka bulan.',
        'duration_months.min'      => 'Durasi tidak boleh negatif. Isi 0 untuk Permanen.',
    ];
}
public function save()
{
    // Menjalankan validasi berdasarkan rules & messages di atas
    $validatedData = $this->validate();

    // Logika pembuatan Title (seperti diskusi sebelumnya)
    $generatedTitle = $this->duration_months > 0
        ? "{$this->name} (expiry in {$this->duration_months} bulan)"
        : "{$this->name} (Permanen)";

   ComplianceMaster::create([
        'name'            => $this->name,
        'description'     => $this->description,
        'class'           => $this->class,
        'duration_months' => ($this->duration_months > 0) ? $this->duration_months : null,
        'title'           => $generatedTitle,
        'status'          => $this->status,
    ]);

    $this->dispatch('alert', ['text' => 'Master data berhasil ditambahkan!']);
    $this->reset();
}
    public function render()
    {
        return view('livewire.administration.compliances.index',[
             'ComplianceMaster' => ComplianceMaster::paginate(20)
        ]);
    }
    public function paginationView()
    {
        return 'paginate.pagination';
    }
}
