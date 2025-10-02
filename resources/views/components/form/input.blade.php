@props([
    'id' => null,
    'name' => '',
    'label' => '',
    'type' => 'text',
    'value' => '',
    'placeholder' => '',
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'help' => null,
    'validation' => true
])

<div class="mb-3">
    @if($label)
        <label for="{{ $id ?? ($name . '_' . uniqid()) }}" class="block font-medium text-sm text-gray-700 mb-1">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    @php $inputId = $id ?? ($name . '_' . uniqid()); @endphp

    <input id="{{ $inputId }}"
           name="{{ $name }}"
           type="{{ $type }}"
           value="{{ old($name, $value) }}"
           placeholder="{{ $placeholder }}"
           {{ $required ? 'required' : '' }}
           {{ $disabled ? 'disabled' : '' }}
           {{ $readonly ? 'readonly' : '' }}
           autocomplete="{{ 
               $type === 'password' ? 'current-password' : 
               ($type === 'email' ? 'email' : 
               ($name === 'name' ? 'name' : 'off')) 
           }}"
           @if($validation && $required)
               class="input-wajib-validasi border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm block w-full px-4 py-2 {{ $disabled || $readonly ? 'bg-gray-100' : '' }}"
           @else
               class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm block w-full px-4 py-2 {{ $disabled || $readonly ? 'bg-gray-100' : '' }}"
           @endif
           {{ $attributes }}>

    <!-- Error Message Container -->
    <div class="pesan-error text-red-500 text-sm mt-1" data-error-for="{{ $inputId }}">
        @error($name){{ $message }}@enderror
    </div>

    @if($help)
        <p class="text-sm text-gray-500 mt-1">{{ $help }}</p>
    @endif
</div>