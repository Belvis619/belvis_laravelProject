<x-layouts.app :title="__('Trash')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">

        <!-- Success Message -->
        @if(session('success'))
            <div class="rounded-lg bg-green-100 p-4 text-green-700 dark:bg-green-900/30 dark:text-green-300">
                {{ session('success') }}
            </div>
        @endif

        <!-- Deleted Students Section -->
        <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex h-full flex-col p-6">
                <h2 class="mb-4 text-lg font-semibold text-neutral-900 dark:text-neutral-100">Deleted Students</h2>
                <div class="overflow-x-auto">
                    <table class="w-full min-w-full">
                        <thead>
                            <tr class="border-b border-neutral-200 bg-neutral-50 dark:border-neutral-700 dark:bg-neutral-900/50">
                                <th class="px-4 py-3 text-left text-sm font-semibold text-neutral-700 dark:text-neutral-300">#</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-neutral-700 dark:text-neutral-300">Avatar</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-neutral-700 dark:text-neutral-300">Name</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-neutral-700 dark:text-neutral-300">Email</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-neutral-700 dark:text-neutral-300">Course</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-neutral-700 dark:text-neutral-300">Deleted At</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-neutral-700 dark:text-neutral-300">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                            @forelse($deletedStudents as $student)
                                <tr class="transition-colors hover:bg-neutral-50 dark:hover:bg-neutral-800/50">
                                    <td class="px-4 py-3 text-sm text-neutral-600 dark:text-neutral-400">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-3">
                                        @if($student->photo)
                                            <img src="{{ asset('storage/' . $student->photo) }}" alt="{{ $student->name }}" class="h-10 w-10 rounded-full object-cover">
                                        @else
                                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-600 text-sm font-medium text-white">
                                                {{ $student->initials() }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-neutral-900 dark:text-neutral-100">{{ $student->name }}</td>
                                    <td class="px-4 py-3 text-sm text-neutral-600 dark:text-neutral-400">{{ $student->email }}</td>
                                    <td class="px-4 py-3 text-sm text-neutral-600 dark:text-neutral-400">
                                        {{ $student->course ? $student->course->course_name : 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-neutral-600 dark:text-neutral-400">
                                        {{ $student->deleted_at->format('M d, Y H:i') }}
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        <form action="{{ route('trash.students.restore', $student->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-green-600 transition-colors hover:text-green-700 dark:text-green-400 dark:hover:text-green-300">
                                                Restore
                                            </button>
                                        </form>
                                        <span class="mx-1 text-neutral-400">|</span>
                                        <form action="{{ route('trash.students.forceDelete', $student->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to permanently delete this student? This action cannot be undone.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 transition-colors hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                                                Delete Permanently
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-sm text-neutral-500 dark:text-neutral-400">
                                        No deleted students found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Deleted Courses Section -->
        <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex h-full flex-col p-6">
                <h2 class="mb-4 text-lg font-semibold text-neutral-900 dark:text-neutral-100">Deleted Courses</h2>
                <div class="overflow-x-auto">
                    <table class="w-full min-w-full">
                        <thead>
                            <tr class="border-b border-neutral-200 bg-neutral-50 dark:border-neutral-700 dark:bg-neutral-900/50">
                                <th class="px-4 py-3 text-left text-sm font-semibold text-neutral-700 dark:text-neutral-300">#</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-neutral-700 dark:text-neutral-300">Course Name</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-neutral-700 dark:text-neutral-300">Description</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-neutral-700 dark:text-neutral-300">Deleted At</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-neutral-700 dark:text-neutral-300">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                            @forelse($deletedCourses as $course)
                                <tr class="transition-colors hover:bg-neutral-50 dark:hover:bg-neutral-800/50">
                                    <td class="px-4 py-3 text-sm text-neutral-600 dark:text-neutral-400">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-3 text-sm text-neutral-900 dark:text-neutral-100">{{ $course->course_name }}</td>
                                    <td class="px-4 py-3 text-sm text-neutral-600 dark:text-neutral-400">
                                        {{ Str::limit($course->description, 50) ?? 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-neutral-600 dark:text-neutral-400">
                                        {{ $course->deleted_at->format('M d, Y H:i') }}
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        <form action="{{ route('trash.courses.restore', $course->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-green-600 transition-colors hover:text-green-700 dark:text-green-400 dark:hover:text-green-300">
                                                Restore
                                            </button>
                                        </form>
                                        <span class="mx-1 text-neutral-400">|</span>
                                        <form action="{{ route('trash.courses.forceDelete', $course->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to permanently delete this course? This action cannot be undone.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 transition-colors hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                                                Delete Permanently
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-sm text-neutral-500 dark:text-neutral-400">
                                        No deleted courses found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
