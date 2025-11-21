<div>
    @if($likelihood_id && $risk_consequence_id)
    <div class="mt-6 p-4 bg-white shadow border rounded-md max-w-xl">
        <h3 class="font-bold mb-2">Edit Cell: L{{ $likelihood_id }} x C{{ $risk_consequence_id }}</h3>

        <div class="space-y-3">
            <div>
                <label>Severity</label>
                <select wire:model="severity" class="w-full border p-2 rounded">
                    <option value="">Select</option>
                    <option>Low</option>
                    <option>Moderate</option>
                    <option>High</option>
                    <option>Extreme</option>
                </select>
            </div>
            <div>
                <label>Description</label>
                <textarea wire:model="description" class="w-full border p-2 rounded"></textarea>
            </div>
            <div>
                <label>Action</label>
                <textarea wire:model="action" class="w-full border p-2 rounded"></textarea>
            </div>
            <button wire:click="save" class="bg-blue-500 text-white px-4 py-2 rounded">Save</button>
        </div>
    </div>
    @endif
</div>
