<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Panel de Control') }} - {{ $role->description ?? $role->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            
            {{-- Si el rol no tiene permisos --}}
            @if($permissions->isEmpty())
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-center">
                            <svg class="w-16 h-16 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            <h3 class="mt-4 text-lg font-semibold text-gray-900">
                                No tienes permisos asignados
                            </h3>
                            <p class="mt-2 text-gray-600">
                                Por favor, contacta al administrador para obtener los permisos necesarios.
                            </p>
                        </div>
                    </div>
                </div>
            @else
                {{-- Módulos disponibles según permisos --}}
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                    
                    {{-- Módulo Docentes --}}
                    @if($permissions->contains('name', 'crear_docente') || $permissions->contains('name', 'ver_docentes'))
                        <div class="overflow-hidden transition duration-200 bg-white shadow-sm sm:rounded-lg hover:shadow-md">
                            <div class="p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="p-3 bg-blue-100 rounded-lg">
                                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                    </div>
                                </div>
                                <h3 class="mb-2 text-lg font-semibold text-gray-900">Docentes</h3>
                                <p class="mb-4 text-sm text-gray-600">Gestionar información de docentes</p>
                                <a href="{{ route('docentes.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-blue-600 rounded-md hover:bg-blue-700">
                                    Ir al módulo
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @endif

                    {{-- Módulo Estadísticas --}}
                    @if($permissions->contains('name', 'ver_estadisticas'))
                        <div class="overflow-hidden transition duration-200 bg-white shadow-sm sm:rounded-lg hover:shadow-md">
                            <div class="p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="p-3 bg-purple-100 rounded-lg">
                                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                        </svg>
                                    </div>
                                </div>
                                <h3 class="mb-2 text-lg font-semibold text-gray-900">Estadísticas</h3>
                                <p class="mb-4 text-sm text-gray-600">Ver reportes y análisis de asistencias</p>
                                <a href="{{ route('estadisticas.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-purple-600 rounded-md hover:bg-purple-700">
                                    Ir al módulo
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @endif

                    {{-- Módulo Materias --}}
                    @if($permissions->contains('name', 'crear_materia') || $permissions->contains('name', 'ver_materias'))
                        <div class="overflow-hidden transition duration-200 bg-white shadow-sm sm:rounded-lg hover:shadow-md">
                            <div class="p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="p-3 bg-green-100 rounded-lg">
                                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                        </svg>
                                    </div>
                                </div>
                                <h3 class="mb-2 text-lg font-semibold text-gray-900">Materias</h3>
                                <p class="mb-4 text-sm text-gray-600">Administrar catálogo de materias</p>
                                <a href="{{ route('materias.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-green-600 rounded-md hover:bg-green-700">
                                    Ir al módulo
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @endif

                    {{-- Módulo Grupos --}}
                    @if($permissions->contains('name', 'crear_grupo') || $permissions->contains('name', 'ver_grupos'))
                        <div class="overflow-hidden transition duration-200 bg-white shadow-sm sm:rounded-lg hover:shadow-md">
                            <div class="p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="p-3 bg-yellow-100 rounded-lg">
                                        <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                    </div>
                                </div>
                                <h3 class="mb-2 text-lg font-semibold text-gray-900">Grupos</h3>
                                <p class="mb-4 text-sm text-gray-600">Gestionar grupos de estudiantes</p>
                                <a href="{{ route('grupos.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-yellow-600 rounded-md hover:bg-yellow-700">
                                    Ir al módulo
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @endif

                    {{-- Módulo Horarios --}}
                    @if($permissions->contains('name', 'crear_horario') || $permissions->contains('name', 'ver_horarios'))
                        <div class="overflow-hidden transition duration-200 bg-white shadow-sm sm:rounded-lg hover:shadow-md">
                            <div class="p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="p-3 bg-indigo-100 rounded-lg">
                                        <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                </div>
                                <h3 class="mb-2 text-lg font-semibold text-gray-900">Horarios</h3>
                                <p class="mb-4 text-sm text-gray-600">Administrar horarios de clases</p>
                                <a href="{{ route('horarios.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-indigo-600 rounded-md hover:bg-indigo-700">
                                    Ir al módulo
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @endif

                    {{-- Módulo Aulas --}}
                    @if($permissions->contains('name', 'crear_aula') || $permissions->contains('name', 'ver_aulas'))
                        <div class="overflow-hidden transition duration-200 bg-white shadow-sm sm:rounded-lg hover:shadow-md">
                            <div class="p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="p-3 bg-red-100 rounded-lg">
                                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                    </div>
                                </div>
                                <h3 class="mb-2 text-lg font-semibold text-gray-900">Aulas</h3>
                                <p class="mb-4 text-sm text-gray-600">Gestionar espacios físicos</p>
                                <a href="{{ route('aulas.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-red-600 rounded-md hover:bg-red-700">
                                    Ir al módulo
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @endif

                    {{-- Módulo Usuarios --}}
                    @if($permissions->contains('name', 'crear_usuario') || $permissions->contains('name', 'ver_usuarios'))
                        <div class="overflow-hidden transition duration-200 bg-white shadow-sm sm:rounded-lg hover:shadow-md">
                            <div class="p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="p-3 bg-pink-100 rounded-lg">
                                        <svg class="w-8 h-8 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                    </div>
                                </div>
                                <h3 class="mb-2 text-lg font-semibold text-gray-900">Usuarios</h3>
                                <p class="mb-4 text-sm text-gray-600">Administrar usuarios del sistema</p>
                                <a href="{{ route('users.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-pink-600 rounded-md hover:bg-pink-700">
                                    Ir al módulo
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @endif

                    {{-- Módulo Roles --}}
                    @if($permissions->contains('name', 'crear_rol') || $permissions->contains('name', 'ver_roles'))
                        <div class="overflow-hidden transition duration-200 bg-white shadow-sm sm:rounded-lg hover:shadow-md">
                            <div class="p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="p-3 bg-gray-100 rounded-lg">
                                        <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                        </svg>
                                    </div>
                                </div>
                                <h3 class="mb-2 text-lg font-semibold text-gray-900">Roles y Permisos</h3>
                                <p class="mb-4 text-sm text-gray-600">Gestionar roles y permisos</p>
                                <a href="{{ route('roles.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-gray-600 rounded-md hover:bg-gray-700">
                                    Ir al módulo
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @endif

                </div>
            @endif
        </div>
    </div>
</x-app-layout>
