#!/bin/bash

# Script de vérification OBLIGATOIRE avant tout test
# À exécuter : bash scripts/verify-test-config.sh

echo "🔍 Vérification de la configuration des tests..."
echo ""

# Vérifier phpunit.xml
echo "1️⃣ Vérification phpunit.xml..."
if grep -q 'name="DB_CONNECTION" value="sqlite"' phpunit.xml; then
    echo "   ✅ phpunit.xml utilise SQLite"
else
    echo "   ❌ ERREUR: phpunit.xml n'utilise PAS SQLite!"
    echo "   ⚠️  NE PAS lancer de tests!"
    exit 1
fi

# Vérifier .env.testing
echo ""
echo "2️⃣ Vérification .env.testing..."
if [ -f .env.testing ]; then
    if grep -q "DB_CONNECTION=sqlite" .env.testing; then
        echo "   ✅ .env.testing existe et utilise SQLite"
    else
        echo "   ❌ ERREUR: .env.testing n'utilise PAS SQLite!"
        exit 1
    fi
else
    echo "   ❌ ERREUR: .env.testing n'existe pas!"
    exit 1
fi

# Vérifier .env (base dev)
echo ""
echo "3️⃣ Vérification .env (base dev)..."
if grep -q "DB_CONNECTION=mysql" .env; then
    echo "   ✅ .env utilise MySQL (base dev)"
else
    echo "   ⚠️  WARNING: .env n'utilise pas MySQL"
fi

# Vérifier que la base dev contient des données
echo ""
echo "4️⃣ Vérification base de développement..."
CLIENT_COUNT=$(mysql -u root -ppassword123 -N -e "SELECT COUNT(*) FROM clients" workshop_pilot 2>/dev/null)

if [ $? -eq 0 ]; then
    if [ "$CLIENT_COUNT" -gt 0 ]; then
        echo "   ✅ Base dev contient $CLIENT_COUNT clients"
    else
        echo "   ⚠️  WARNING: Base dev est vide!"
    fi
else
    echo "   ⚠️  WARNING: Impossible de vérifier la base dev"
fi

echo ""
echo "✅ Configuration validée - Tests AUTORISÉS"
echo ""
echo "Commandes sûres :"
echo "  php artisan test"
echo "  php artisan test --filter=NomTest"
echo ""
