<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation de mot de passe</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="max-w-lg w-full bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <h1 class="block text-gray-700 text-xl font-bold mb-2">Réinitialisation de mot de passe MEMENTOS</h1>
        <p class="text-gray-700 text-base">Bonjour,</p>
        <p class="text-gray-700 text-base">Vous avez demandé une réinitialisation de mot de passe. Cliquez sur le lien
            ci-dessous pour réinitialiser votre mot de passe :</p>
        <a href="{{ $resetLink }}"
            class="block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline mt-4 text-center">
            Réinitialiser mon mot de passe
        </a>
        <p class="text-gray-600 text-sm mt-2">Si vous n'avez pas demandé cette réinitialisation, veuillez ignorer cet
            email.</p>
    </div>
</body>

</html>