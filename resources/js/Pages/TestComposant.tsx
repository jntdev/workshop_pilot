import { useState } from 'react';
import MainLayout from '@/Layouts/MainLayout';
import { Head } from '@inertiajs/react';
import Input from '@/Components/ui/Input';

export default function TestComposant() {
    const [textValue, setTextValue] = useState('');
    const [numberValue, setNumberValue] = useState('');

    return (
        <MainLayout>
            <Head title="Test Composant" />

            <div className="page-header">
                <h1>Test Composant Input</h1>
            </div>

            <div className="test-composant">
                <div className="test-composant__section">
                    <h2>Input type="text"</h2>
                    <Input
                        type="text"
                        label="Nom"
                        name="nom"
                        value={textValue}
                        onChange={(e) => setTextValue(e.target.value)}
                        placeholder="Entrez votre nom"
                    />
                    <p>Valeur: {textValue || '(vide)'}</p>
                </div>

                <div className="test-composant__section">
                    <h2>Input type="number"</h2>
                    <Input
                        type="number"
                        label="Quantité"
                        name="quantite"
                        value={numberValue}
                        onChange={(e) => setNumberValue(e.target.value)}
                        placeholder="0"
                        step="0.01"
                    />
                    <p>Valeur: {numberValue || '(vide)'}</p>
                </div>

                <div className="test-composant__section">
                    <h2>Input avec erreur</h2>
                    <Input
                        type="text"
                        label="Email"
                        name="email"
                        error="Ce champ est requis"
                        placeholder="exemple@email.com"
                    />
                </div>

                <div className="test-composant__section">
                    <h2>Input désactivé</h2>
                    <Input
                        type="text"
                        label="Champ désactivé"
                        name="disabled"
                        value="Non modifiable"
                        disabled
                    />
                </div>
            </div>
        </MainLayout>
    );
}
