@props(['type' => 'info'])

{{-- resources/views/components/alert.blade.php (Sesuai Slide 23 Pertemuan 5) --}}
<div class="alert alert-{{ $type }} alert-dismissible fade show shadow-sm" role="alert">
    {{ $slot }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
