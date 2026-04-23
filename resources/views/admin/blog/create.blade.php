<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-3xl text-amber-900">Create blog post</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white border-2 border-amber-100 rounded-[2rem] p-8 shadow-sm">
                @include('admin.blog._form', [
                    'action' => route('admin.blog.store'),
                    'method' => 'POST',
                    'submitLabel' => 'Create post',
                ])
            </div>
        </div>
    </div>
</x-app-layout>
