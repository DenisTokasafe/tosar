<?php

namespace App\Livewire\Administration\Translation;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Translation;
use Illuminate\Support\Facades\Cache;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $translationId, $key, $en, $id_text, $group = 'general';
    public $isEditing = false;

    protected $rules = [
        'key' => 'required',
        'en' => 'required',
        'id_text' => 'required',
    ];

    public function render()
    {
        $translations = Translation::where('key', 'like', '%' . $this->search . '%')
            ->orWhere('en', 'like', '%' . $this->search . '%')
            ->orWhere('id_text', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(10);

        return view('livewire.administration.translation.index', [
            'translations' => $translations
        ]);
    }

    public function resetFields()
    {
        $this->key = '';
        $this->en = '';
        $this->id_text = '';
        $this->translationId = null;
        $this->isEditing = false;
    }

    public function store()
    {
        $this->validate();

        Translation::updateOrCreate(
            ['id' => $this->translationId],
            [
                'key' => $this->key,
                'en' => $this->en,
                'id_text' => $this->id_text,
                'group' => $this->group
            ]
        );

        $this->clearTranslationCache();
        $this->resetFields();
        session()->flash('message', $this->translationId ? 'Data diperbarui!' : 'Data ditambahkan!');
    }

    public function edit($id)
    {
        $data = Translation::findOrFail($id);
        $this->translationId = $id;
        $this->key = $data->key;
        $this->en = $data->en;
        $this->id_text = $data->id_text;
        $this->isEditing = true;
    }

    public function delete($id)
    {
        Translation::find($id)->delete();
        $this->clearTranslationCache();
        session()->flash('message', 'Data dihapus!');
    }

    private function clearTranslationCache()
    {
        Cache::forget('translations_json_en');
        Cache::forget('translations_json_id');
    }
}
