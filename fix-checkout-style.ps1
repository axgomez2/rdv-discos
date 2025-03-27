$checkoutPath = "resources\views\site\checkout\index.blade.php"
$content = Get-Content $checkoutPath -Raw

# Substituir classes Bootstrap por classes Tailwind
$replacements = @{
    # Container e grid
    'container py-5' = 'mx-auto max-w-screen-xl px-4 2xl:px-0 py-8'
    'row' = 'mt-6 sm:mt-8 md:gap-6 lg:flex lg:items-start xl:gap-8'
    'col-md-5 order-md-2 mb-4' = 'mx-auto mt-6 max-w-4xl flex-1 space-y-6 lg:mt-0 lg:w-full lg:max-w-sm'
    'col-md-7 order-md-1' = 'mx-auto w-full flex-none lg:max-w-2xl xl:max-w-3xl'
    
    # Alertas e notificações
    'alert alert-danger' = 'mt-4 p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50'
    'alert alert-info' = 'mt-4 p-4 mb-4 text-sm text-blue-800 rounded-lg bg-blue-50'
    'alert alert-warning' = 'mt-4 p-4 mb-4 text-sm text-yellow-800 rounded-lg bg-yellow-50'
    
    # Cards e listas
    'list-group mb-3' = 'space-y-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm sm:p-6'
    'list-group-item' = 'flex items-center justify-between border-b border-gray-200 pb-3'
    'list-group-item d-flex justify-content-between' = 'flex items-center justify-between pt-3'
    'list-group-item d-flex justify-content-between bg-light' = 'flex items-center justify-between border-t border-gray-200 pt-3'
    
    # Botões
    'btn btn-primary' = 'w-full text-white bg-primary-600 hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center'
    'btn btn-secondary' = 'text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-4 py-2'
    
    # Formulários
    'form-control' = 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5'
    'mb-3' = 'mb-4'
    
    # Texto
    'text-muted' = 'text-sm text-gray-500'
    'text-success' = 'text-lg font-semibold text-emerald-600'
}

foreach ($old in $replacements.Keys) {
    $new = $replacements[$old]
    $content = $content -replace $old, $new
}

# Adicionar classes Tailwind aos cabeçalhos
$content = $content -replace '<h1 class="mb-4">', '<h2 class="text-xl font-semibold text-gray-900 sm:text-2xl">'
$content = $content -replace '<h4 class="mb-3">', '<p class="text-xl font-semibold text-gray-900">'

# Salvar o arquivo modificado
$content | Set-Content $checkoutPath

Write-Host "Estilo do checkout atualizado para Tailwind CSS com sucesso!"
