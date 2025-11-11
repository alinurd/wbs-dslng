 <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
     <div>
         <x-label for="kelompok" value="Kelompok" />
         <x-input wire:model.defer="kelompok" id="kelompok" class="w-full" />
         @error('kelompok')
             <span class="text-red-500 text-sm">{{ $message }}</span>
         @enderror
     </div>

     <div>
         <x-label for="data" value="Data" />
         <x-input wire:model.defer="data" id="data" class="w-full" />
         @error('data')
             <span class="text-red-500 text-sm">{{ $message }}</span>
         @enderror
     </div>

     <div>
         <x-label for="param_int" value="Param Int" />
         <x-input wire:model.defer="param_int" id="param_int" type="number" class="w-full" />
         @error('param_int')
             <span class="text-red-500 text-sm">{{ $message }}</span>
         @enderror
     </div>

     <div>
         <x-label for="param_str" value="Param String" />
         <x-input wire:model.defer="param_str" id="param_str" class="w-full" />
     </div>

     <div class="flex items-center gap-2 mt-6">
         <x-checkbox wire:model.defer="is_active" id="is_active_form" />
         <x-label for="is_active_form" value="Aktif" />
     </div>
 </div>
