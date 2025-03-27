$checkoutPath = "resources\views\site\checkout\index.blade.php"
$content = Get-Content $checkoutPath -Raw

# Remover @extends, @section, @endsection
$content = $content -replace '@extends\(.*\)', '<x-app-layout>'
$content = $content -replace '@section\(.*\)', ''
$content = $content -replace '@endsection', '</x-app-layout>'

# Salvar o arquivo modificado
$content | Set-Content $checkoutPath

Write-Host "Layout do checkout corrigido com sucesso!"
