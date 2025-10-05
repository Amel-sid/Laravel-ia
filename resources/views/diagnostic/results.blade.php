@extends('layouts.diagnostic')

@section('title', 'Résultats du diagnostic - ' . $assessment->company_name)
@section('breadcrumb', 'Résultats du diagnostic')

@section('content')
    <div class="max-w-6xl mx-auto px-4 py-8">

        <!-- Header avec score global -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white rounded-xl p-8 mb-8">
            <div class="text-center">
                <h1 class="text-3xl font-bold mb-4">🎯 Rapport de Maturité Cyber</h1>

                <div class="text-xl mb-2">{{ $assessment->company_name }}</div>
                @if($assessment->sector)
                    <div class="text-blue-100 mb-4">Secteur: {{ ucfirst($assessment->sector) }}</div>
                @endif

                <!-- Score global -->
                @php
                    $totalMaxPoints = collect($domainStats)->sum('max_score');
                    $globalPercentage = $totalMaxPoints > 0 ? round(($assessment->total_score / $totalMaxPoints) * 100) : 0;
                @endphp

                <div class="bg-white bg-opacity-20 rounded-lg p-6 inline-block">
                    <div class="text-4xl font-bold mb-2">{{ $globalPercentage }}%</div>
                    <div class="text-lg">Maturité globale</div>
                    <div class="text-sm opacity-75 mt-1">
                        {{ $assessment->total_score }}/{{ $totalMaxPoints }} points
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats par domaine -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            @foreach($domainStats as $domainKey => $stats)
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <div class="text-center">
                        <h3 class="font-semibold text-gray-800 mb-4">{{ $stats['label'] }}</h3>

                        <!-- Cercle de progression simple -->
                        <div class="relative w-24 h-24 mx-auto mb-4">
                            <div class="w-full h-full bg-gray-200 rounded-full">
                                <div class="w-full h-full bg-{{ $stats['color'] }}-500 rounded-full"
                                     style="clip-path: polygon(50% 50%, 50% 0%, {{ 50 + ($stats['percentage'] * 0.5) }}% 0%, 100% 100%, 0% 100%);">
                                </div>
                            </div>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <span class="text-xl font-bold text-gray-700">{{ $stats['percentage'] }}%</span>
                            </div>
                        </div>

                        <div class="text-sm text-gray-600">
                            {{ $stats['score'] }}/{{ $stats['max_score'] }} points
                        </div>
                        <div class="text-xs text-gray-500 mt-1">
                            {{ $stats['questions_count'] }} questions
                        </div>

                        <!-- Niveau -->
                        <div class="mt-3">
                            @if($stats['percentage'] >= 80)
                                <span class="bg-green-100 text-green-800 text-sm px-3 py-1 rounded-full">Excellent</span>
                            @elseif($stats['percentage'] >= 60)
                                <span class="bg-blue-100 text-blue-800 text-sm px-3 py-1 rounded-full">Bon</span>
                            @elseif($stats['percentage'] >= 40)
                                <span class="bg-yellow-100 text-yellow-800 text-sm px-3 py-1 rounded-full">Moyen</span>
                            @elseif($stats['percentage'] >= 20)
                                <span class="bg-orange-100 text-orange-800 text-sm px-3 py-1 rounded-full">Faible</span>
                            @else
                                <span class="bg-red-100 text-red-800 text-sm px-3 py-1 rounded-full">Critique</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Recommandations rapides -->
        <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">🚀 Recommandations prioritaires</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($domainStats as $domainKey => $stats)
                    @if($stats['percentage'] < 60)
                        <div class="border-l-4 border-{{ $stats['color'] }}-500 pl-4">
                            <h3 class="font-semibold text-gray-800 mb-2">{{ $stats['label'] }}</h3>
                            <p class="text-gray-600 text-sm mb-2">Score: {{ $stats['percentage'] }}% - Nécessite une attention</p>

                            @if($domainKey === 'gouvernance')
                                <ul class="text-sm text-gray-700 space-y-1">
                                    <li>• Formaliser une politique de sécurité</li>
                                    <li>• Sensibiliser les équipes</li>
                                    <li>• Effectuer une analyse de risques</li>
                                </ul>
                            @elseif($domainKey === 'access')
                                <ul class="text-sm text-gray-700 space-y-1">
                                    <li>• Déployer l'authentification multi-facteur</li>
                                    <li>• Réviser les droits d'accès</li>
                                    <li>• Renforcer la politique de mots de passe</li>
                                </ul>
                            @elseif($domainKey === 'protection')
                                <ul class="text-sm text-gray-700 space-y-1">
                                    <li>• Améliorer la protection antivirus/EDR</li>
                                    <li>• Automatiser les mises à jour</li>
                                    <li>• Segmenter le réseau</li>
                                </ul>
                            @else
                                <ul class="text-sm text-gray-700 space-y-1">
                                    <li>• Mettre en place des sauvegardes testées</li>
                                    <li>• Créer un plan de continuité</li>
                                    <li>• Définir des procédures d'incident</li>
                                </ul>
                            @endif
                        </div>
                    @endif
                @endforeach

                @if(collect($domainStats)->every(fn($stats) => $stats['percentage'] >= 60))
                    <div class="col-span-2 text-center">
                        <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                            <div class="text-green-800 font-semibold mb-2">🎉 Excellente maturité !</div>
                            <p class="text-green-700">Votre organisation présente un bon niveau de sécurité dans tous les domaines. Continuez vos efforts et pensez à maintenir cette vigilance.</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Actions -->
        <div class="text-center">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Prochaines étapes</h3>

            <div class="space-x-4">
                <button onclick="window.print()" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition">
                    📄 Imprimer le rapport
                </button>

                <a href="{{ route('diagnostic.start') }}" class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition inline-block">
                    🔄 Refaire le diagnostic
                </a>
            </div>

            <div class="mt-6 text-sm text-gray-600">
                Diagnostic réalisé le {{ $assessment->created_at->format('d/m/Y à H:i') }}
            </div>
        </div>

    </div>
@endsection
