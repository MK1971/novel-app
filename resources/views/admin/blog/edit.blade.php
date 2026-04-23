<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-3xl text-amber-900">Edit blog post</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white border-2 border-amber-100 rounded-[2rem] p-8 shadow-sm">
                @include('admin.blog._form', [
                    'post' => $blogPost,
                    'action' => route('admin.blog.update', $blogPost),
                    'method' => 'PUT',
                    'submitLabel' => 'Save changes',
                ])
            </div>
        </div>
    </div>
</x-app-layout>
