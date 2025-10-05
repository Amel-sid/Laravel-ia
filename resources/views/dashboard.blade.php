<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Tableau de bord
                </h2>
                <p class="text-gray-600 text-sm mt-1">
                    Bienvenue, {{ $user->name }} 👋
                </p>
            </div>
            <div class="flex items-center space-x-4">
                <div class="text-right text-sm text-gray-500">
                    <p>Dernière activité</p>
                    <p class="font-medium text-gray-700">
                        {{ $user->last_activity_at ? $user->last_activity_at->diffForHumans() : 'Aujourd\'hui' }}
                    </p>
                </div>
                <a href="{{ route('assistant.start') }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    <span>Nouveau document</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <!-- Alertes et actions urgentes -->
            @if(isset($stats['urgent_alerts']))
                @php $alerts = $stats['urgent_alerts']; @endphp
                @if($alerts['total_count'] > 0)
                    <div class="bg-white shadow-sm rounded-xl border border-gray-100 
                        {{ $alerts['has_critical'] ? 'border-red-300 shadow-red-100' : '' }}">
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-4">
                                <div class="flex items-center">
                                    <h3 class="text-lg font-semibold text-gray-900 mr-3">Alertes et actions urgentes</h3>
                                    @if($alerts['has_critical'])
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.232 15.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                            </svg>
                                            Critique
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $alerts['total_count'] }} notification(s)
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="space-y-4">
                                <!-- Alertes -->
                                @foreach($alerts['alerts'] as $alert)
                                    <div class="flex items-start p-4 border border-gray-100 rounded-lg
                                        {{ $alert['type'] === 'critical' ? 'bg-red-50 border-red-200' : 
                                           ($alert['type'] === 'warning' ? 'bg-yellow-50 border-yellow-200' : 
                                           ($alert['type'] === 'success' ? 'bg-green-50 border-green-200' : 'bg-blue-50 border-blue-200')) }}">
                                        
                                        <div class="flex-shrink-0 mr-3">
                                            @if($alert['type'] === 'critical')
                                                <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center">
                                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.232 15.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                                    </svg>
                                                </div>
                                            @elseif($alert['type'] === 'warning')
                                                <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                </div>
                                            @elseif($alert['type'] === 'success')
                                                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                </div>
                                            @else
                                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="flex-1">
                                            <div class="flex items-center justify-between">
                                                <h4 class="font-medium text-gray-900">{{ $alert['title'] }}</h4>
                                                <span class="text-xs text-gray-500">
                                                    {{ $alert['created_at']->diffForHumans() }}
                                                </span>
                                            </div>
                                            <p class="text-sm text-gray-700 mt-1">{{ $alert['message'] }}</p>
                                            @if($alert['action'])
                                                <a href="{{ $alert['action_url'] }}" 
                                                   class="inline-flex items-center mt-2 text-sm font-medium 
                                                   {{ $alert['type'] === 'critical' ? 'text-red-700 hover:text-red-800' : 
                                                      ($alert['type'] === 'warning' ? 'text-yellow-700 hover:text-yellow-800' : 
                                                      ($alert['type'] === 'success' ? 'text-green-700 hover:text-green-800' : 'text-blue-700 hover:text-blue-800')) }}">
                                                    {{ $alert['action'] }}
                                                    <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                    </svg>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach

                                <!-- Actions urgentes -->
                                @foreach($alerts['urgent_actions'] as $action)
                                    <div class="flex items-center p-4 border-2 border-dashed border-orange-200 rounded-lg bg-orange-50">
                                        <div class="flex-shrink-0 mr-3">
                                            <div class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center">
                                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between mb-2">
                                                <h4 class="font-medium text-gray-900 flex items-center">
                                                    {{ $action['title'] }}
                                                    <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                        Priorité {{ ucfirst($action['priority']) }}
                                                    </span>
                                                </h4>
                                                <span class="text-xs text-gray-600">
                                                    Échéance: {{ $action['deadline'] }}
                                                </span>
                                            </div>
                                            <p class="text-sm text-gray-700 mb-3">{{ $action['description'] }}</p>
                                            
                                            <!-- Barre de progression -->
                                            <div class="mb-3">
                                                <div class="flex justify-between text-xs text-gray-600 mb-1">
                                                    <span>Progression</span>
                                                    <span>{{ round($action['progress']) }}%</span>
                                                </div>
                                                <div class="bg-gray-200 rounded-full h-2">
                                                    <div class="bg-orange-500 h-2 rounded-full transition-all duration-300" 
                                                         style="width: {{ $action['progress'] }}%"></div>
                                                </div>
                                            </div>
                                            
                                            <a href="{{ $action['action_url'] }}" 
                                               class="inline-flex items-center text-sm font-medium text-orange-700 hover:text-orange-800">
                                                {{ $action['action'] }}
                                                <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            @endif

            <!-- Statistiques principales -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total documents -->
                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-2xl font-bold text-gray-900">{{ $stats['total_documents'] ?? 0 }}</p>
                                <p class="text-sm text-gray-600">Documents générés</p>
                                @if(($stats['total_documents'] ?? 0) > 0)
                                    <p class="text-xs text-green-600 mt-1">✅ Actif</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Documents cette semaine -->
                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-2xl font-bold text-gray-900">{{ $stats['recent_documents'] ?? 0 }}</p>
                                <p class="text-sm text-gray-600">Cette semaine</p>
                                @if(($stats['recent_documents'] ?? 0) > 0)
                                    <p class="text-xs text-green-600 mt-1">🔥 Productif</p>
                                @else
                                    <p class="text-xs text-gray-500 mt-1">💡 À relancer</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Type favori -->
                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                    @php $favoriteType = $stats['favorite_type'] ?? null; @endphp
                                    @if($favoriteType === 'pssi')
                                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                        </svg>
                                    @elseif($favoriteType === 'charte')
                                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                    @elseif($favoriteType === 'sauvegarde')
                                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                                        </svg>
                                    @else
                                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    @endif
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-lg font-bold text-gray-900 capitalize">
                                    @if($favoriteType === 'pssi')
                                        PSSI
                                    @elseif($favoriteType === 'charte')
                                        Charte
                                    @elseif($favoriteType === 'sauvegarde')
                                        Sauvegarde
                                    @else
                                        Aucun
                                    @endif
                                </p>
                                <p class="text-sm text-gray-600">Type préféré</p>
                                @if($favoriteType)
                                    <p class="text-xs text-purple-600 mt-1">🎯 Spécialité</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Score de maturité sécurité -->
                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                @php 
                                    $securityScore = $stats['security_score'] ?? ['score' => 0, 'level' => 'Débutant', 'color' => 'red'];
                                    $colorClasses = [
                                        'red' => 'bg-red-100 text-red-600',
                                        'yellow' => 'bg-yellow-100 text-yellow-600', 
                                        'blue' => 'bg-blue-100 text-blue-600',
                                        'green' => 'bg-green-100 text-green-600'
                                    ];
                                    $bgClass = str_replace('text-', 'bg-', $colorClasses[$securityScore['color']]);
                                    $textClass = $colorClasses[$securityScore['color']];
                                @endphp
                                <div class="w-12 h-12 {{ $bgClass }} rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 {{ $textClass }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-2xl font-bold text-gray-900">{{ $securityScore['score'] }}%</p>
                                <p class="text-sm text-gray-600">Score de maturité</p>
                                <p class="text-xs {{ $textClass }} mt-1">🛡️ {{ $securityScore['level'] }}</p>
                            </div>
                        </div>
                        <!-- Barre de progression -->
                        <div class="mt-4">
                            <div class="bg-gray-200 rounded-full h-2">
                                <div class="h-2 rounded-full transition-all duration-300 {{ str_replace('text-', 'bg-', $textClass) }}" 
                                     style="width: {{ $securityScore['score'] }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statut de conformité -->
            @if(isset($stats['compliance_status']))
                @php $compliance = $stats['compliance_status']; @endphp
                <div class="bg-white shadow-sm rounded-xl border border-gray-100">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Statut de conformité</h3>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                {{ $compliance['color'] === 'green' ? 'bg-green-100 text-green-800' : 
                                   ($compliance['color'] === 'yellow' ? 'bg-yellow-100 text-yellow-800' : 
                                   ($compliance['color'] === 'blue' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800')) }}">
                                {{ $compliance['status'] }} ({{ round($compliance['percentage']) }}%)
                            </span>
                        </div>

                        <!-- Barre de progression globale -->
                        <div class="mb-6">
                            <div class="bg-gray-200 rounded-full h-3">
                                <div class="h-3 rounded-full transition-all duration-500 
                                    {{ $compliance['color'] === 'green' ? 'bg-green-500' : 
                                       ($compliance['color'] === 'yellow' ? 'bg-yellow-500' : 
                                       ($compliance['color'] === 'blue' ? 'bg-blue-500' : 'bg-red-500')) }}" 
                                     style="width: {{ $compliance['percentage'] }}%">
                                </div>
                            </div>
                            <p class="text-sm text-gray-600 mt-2">
                                {{ round($compliance['percentage']) }}% des exigences de base complétées
                                @if($compliance['missing_count'] > 0)
                                    • {{ $compliance['missing_count'] }} document(s) manquant(s)
                                @endif
                            </p>
                        </div>

                        <!-- Liste des exigences -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            @foreach($compliance['requirements'] as $req)
                                <div class="flex items-center p-3 border border-gray-100 rounded-lg 
                                    {{ $req['completed'] ? 'bg-green-50 border-green-200' : 'bg-gray-50' }}">
                                    <div class="flex-shrink-0 mr-3">
                                        @if($req['completed'])
                                            <div class="w-6 h-6 bg-green-500 rounded-full flex items-center justify-center">
                                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </div>
                                        @else
                                            <div class="w-6 h-6 bg-gray-300 rounded-full flex items-center justify-center">
                                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-medium text-sm text-gray-900">{{ $req['name'] }}</p>
                                        <p class="text-xs text-gray-600 mt-1">{{ $req['description'] }}</p>
                                        @if(!$req['completed'])
                                            <a href="{{ route('assistant.start') }}" 
                                               class="text-xs text-blue-600 hover:text-blue-800 mt-1 inline-block">
                                                Créer ce document →
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if($compliance['missing_count'] > 0)
                            <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <p class="text-sm text-blue-800">
                                        <strong>Conseil :</strong> Complétez les documents manquants pour améliorer votre niveau de conformité.
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Actions rapides -->
            <div class="bg-white shadow-sm rounded-xl border border-gray-100">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions rapides</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <a href="{{ route('assistant.start') }}"
                           class="group flex items-center p-4 border-2 border-dashed border-gray-200 rounded-lg hover:border-blue-300 hover:bg-blue-50 transition-all">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-blue-100 group-hover:bg-blue-200 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">Créer un document</p>
                                    <p class="text-sm text-gray-600">PSSI, Charte, Sauvegarde</p>
                                </div>
                            </div>
                        </a>

                        <a href="{{ route('assistant.start') }}"
                           class="group flex items-center p-4 border-2 border-dashed border-gray-200 rounded-lg hover:border-purple-300 hover:bg-purple-50 transition-all">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-purple-100 group-hover:bg-purple-200 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">Mes documents</p>
                                    <p class="text-sm text-gray-600">{{ ($stats['total_documents'] ?? 0) }} document(s)</p>
                                </div>
                            </div>
                        </a>

                        <a href="{{ route('assistant.start') }}"
                           class="group flex items-center p-4 border-2 border-dashed border-gray-200 rounded-lg hover:border-green-300 hover:bg-green-50 transition-all">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-green-100 group-hover:bg-green-200 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">Diagnostic cyber</p>
                                    <p class="text-sm text-gray-600">Évaluer votre sécurité</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Documents récents -->
            <div class="bg-white shadow-sm rounded-xl border border-gray-100">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">Mes documents récents</h3>
                        @if(isset($documents) && $documents->count() > 5)
                            <a href="{{ route('assistant.start') }}"
                               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                Voir tout ({{ $stats['total_documents'] ?? 0 }}) →
                            </a>
                        @endif
                    </div>

                    @if(isset($documents) && $documents->count() > 0)
                        <div class="space-y-4">
                            @foreach($documents as $document)
                                <div class="flex items-center justify-between p-4 border border-gray-100 rounded-lg hover:bg-gray-50 transition-colors">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-shrink-0">
                                            @if($document->type === 'pssi')
                                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                                    </svg>
                                                </div>
                                            @elseif($document->type === 'charte')
                                                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                                    </svg>
                                                </div>
                                            @elseif($document->type === 'sauvegarde')
                                                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-gray-900">{{ $document->title }}</h4>
                                            <div class="flex items-center space-x-4 text-sm text-gray-600 mt-1">
                                                <span class="capitalize">{{ ucfirst($document->type) }}</span>
                                                <span>•</span>
                                                <span>{{ $document->created_at->format('d/m/Y à H:i') }}</span>
                                                <span>•</span>
                                                <span>{{ $document->word_count ?? str_word_count($document->content) }} mots</span>
                                                <span>•</span>
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs
                                                    {{ $document->status === 'generated' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                    {{ ucfirst($document->status) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        @if($document->preview_token)
                                            <a href="{{ route('assistant.preview', $document->preview_token) }}"
                                               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                                Aperçu
                                            </a>
                                            <span class="text-gray-300">•</span>
                                            <a href="{{ route('download.word', $document->preview_token) }}"
                                               class="text-green-600 hover:text-green-800 text-sm font-medium">
                                                Word
                                            </a>
                                            <span class="text-gray-300">•</span>
                                            <a href="{{ route('download.pdf', $document->preview_token) }}"
                                               class="text-red-600 hover:text-red-800 text-sm font-medium">
                                                PDF
                                            </a>
                                        @else
                                            <span class="text-gray-400 text-sm">Actions non disponibles</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="mt-4 text-lg font-medium text-gray-900">Aucun document généré</h3>
                            <p class="mt-2 text-gray-600">Commencez par créer votre premier document de cybersécurité.</p>
                            <div class="mt-6">
                                <a href="{{ route('assistant.start') }}"
                                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center space-x-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    <span>Créer mon premier document</span>
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Informations compte -->
            <div class="bg-white shadow-sm rounded-xl border border-gray-100">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations du compte</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Utilisateur</label>
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <span class="text-blue-600 font-medium text-sm">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                </div>
                                <div>
                                    <p class="text-gray-900 font-medium">{{ $user->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                </div>
                            </div>
                            @if($user->email_verified_at)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-green-100 text-green-800 mt-2">
                                    ✓ Email vérifié
                                </span>
                            @endif
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Membre depuis</label>
                            <p class="text-gray-900">{{ $user->created_at->format('d F Y') }}</p>
                            <p class="text-sm text-gray-500 mt-1">
                                {{ $user->created_at->diffForHumans() }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                            <div class="flex items-center space-x-2">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-green-100 text-green-800">
                                    🎯 Membre Actif
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">
                                Accès complet aux fonctionnalités
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
