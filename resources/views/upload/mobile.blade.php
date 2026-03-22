<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Envoyer des photos - Workshop Pilot</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
            color: #333;
        }

        .container {
            max-width: 400px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            padding: 24px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }

        .header {
            text-align: center;
            margin-bottom: 24px;
        }

        .header h1 {
            font-size: 24px;
            color: #333;
            margin-bottom: 8px;
        }

        .header p {
            color: #666;
            font-size: 14px;
        }

        .remaining {
            text-align: center;
            padding: 12px;
            background: #f0f4ff;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 14px;
            color: #5c6bc0;
        }

        .upload-buttons {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 24px;
        }

        .upload-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 16px 24px;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .upload-btn--camera {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .upload-btn--gallery {
            background: #f5f5f5;
            color: #333;
        }

        .upload-btn:active {
            transform: scale(0.98);
        }

        .upload-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .upload-btn svg {
            width: 24px;
            height: 24px;
        }

        .previews {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
            margin-bottom: 20px;
        }

        .preview-item {
            position: relative;
            aspect-ratio: 1;
            border-radius: 8px;
            overflow: hidden;
            background: #f0f0f0;
        }

        .preview-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .preview-item--uploading::after {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(0,0,0,0.5);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .preview-item .spinner {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 24px;
            height: 24px;
            border: 3px solid rgba(255,255,255,0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: translate(-50%, -50%) rotate(360deg); }
        }

        .preview-item .check {
            position: absolute;
            bottom: 4px;
            right: 4px;
            width: 20px;
            height: 20px;
            background: #4caf50;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .preview-item .check svg {
            width: 12px;
            height: 12px;
            color: white;
        }

        .message {
            text-align: center;
            padding: 16px;
            border-radius: 12px;
            margin-bottom: 16px;
            font-size: 14px;
        }

        .message--success {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .message--error {
            background: #ffebee;
            color: #c62828;
        }

        .message--info {
            background: #e3f2fd;
            color: #1565c0;
        }

        .done-message {
            text-align: center;
            padding: 24px;
            color: #666;
        }

        input[type="file"] {
            display: none;
        }

        .progress-bar {
            height: 4px;
            background: #e0e0e0;
            border-radius: 2px;
            margin-bottom: 16px;
            overflow: hidden;
        }

        .progress-bar__fill {
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: width 0.3s;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Workshop Pilot</h1>
            <p>Envoyez vos photos</p>
        </div>

        <div class="remaining" id="remaining">
            <span id="remainingCount">{{ $remainingUses }}</span> photo(s) restante(s)
        </div>

        <div class="progress-bar" id="progressBar" style="display: none;">
            <div class="progress-bar__fill" id="progressFill"></div>
        </div>

        <div class="upload-buttons">
            <label class="upload-btn upload-btn--camera" id="cameraBtn">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Prendre une photo
                <input type="file" id="cameraInput" accept="image/*" capture="environment">
            </label>

            <label class="upload-btn upload-btn--gallery" id="galleryBtn">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Choisir dans la galerie
                <input type="file" id="galleryInput" accept="image/*" multiple>
            </label>
        </div>

        <div class="previews" id="previews"></div>

        <div id="messageContainer"></div>

        <div class="done-message" id="doneMessage" style="display: none;">
            Vous pouvez fermer cette page.
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/browser-image-compression@2.0.2/dist/browser-image-compression.min.js"></script>
    <script>
        const token = @json($token);
        const uploadUrl = `/api/upload/${token}`;
        let remainingUses = {{ $remainingUses }};

        const cameraInput = document.getElementById('cameraInput');
        const galleryInput = document.getElementById('galleryInput');
        const previews = document.getElementById('previews');
        const messageContainer = document.getElementById('messageContainer');
        const remainingCount = document.getElementById('remainingCount');
        const progressBar = document.getElementById('progressBar');
        const progressFill = document.getElementById('progressFill');
        const doneMessage = document.getElementById('doneMessage');
        const cameraBtn = document.getElementById('cameraBtn');
        const galleryBtn = document.getElementById('galleryBtn');

        function showMessage(text, type = 'info') {
            messageContainer.innerHTML = `<div class="message message--${type}">${text}</div>`;
            setTimeout(() => {
                if (messageContainer.querySelector('.message')) {
                    messageContainer.innerHTML = '';
                }
            }, 4000);
        }

        function updateRemaining(count) {
            remainingUses = count;
            remainingCount.textContent = count;

            if (count <= 0) {
                cameraBtn.style.pointerEvents = 'none';
                cameraBtn.style.opacity = '0.5';
                galleryBtn.style.pointerEvents = 'none';
                galleryBtn.style.opacity = '0.5';
                doneMessage.style.display = 'block';
            }
        }

        async function compressImage(file) {
            const options = {
                maxSizeMB: 1,
                maxWidthOrHeight: 1920,
                useWebWorker: true,
            };

            try {
                return await imageCompression(file, options);
            } catch (error) {
                console.error('Compression error:', error);
                return file;
            }
        }

        function addPreview(file, id) {
            const div = document.createElement('div');
            div.className = 'preview-item preview-item--uploading';
            div.id = `preview-${id}`;
            div.innerHTML = `
                <img src="${URL.createObjectURL(file)}" alt="Preview">
                <div class="spinner"></div>
            `;
            previews.appendChild(div);
        }

        function markPreviewSuccess(id) {
            const div = document.getElementById(`preview-${id}`);
            if (div) {
                div.classList.remove('preview-item--uploading');
                const spinner = div.querySelector('.spinner');
                if (spinner) spinner.remove();
                div.innerHTML += `
                    <div class="check">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                `;
            }
        }

        function markPreviewError(id) {
            const div = document.getElementById(`preview-${id}`);
            if (div) {
                div.remove();
            }
        }

        async function uploadFile(file, id) {
            const compressed = await compressImage(file);

            const formData = new FormData();
            formData.append('photo', compressed, file.name);

            try {
                const response = await fetch(uploadUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: formData,
                });

                const data = await response.json();

                if (response.ok) {
                    markPreviewSuccess(id);
                    updateRemaining(data.remaining_uses);
                    showMessage('Photo envoyee !', 'success');
                } else {
                    markPreviewError(id);
                    showMessage(data.error || 'Erreur d\'envoi', 'error');
                }
            } catch (error) {
                markPreviewError(id);
                showMessage('Erreur de connexion', 'error');
            }
        }

        async function handleFiles(files) {
            const fileArray = Array.from(files);
            const toUpload = fileArray.slice(0, remainingUses);

            if (toUpload.length < fileArray.length) {
                showMessage(`Seulement ${remainingUses} photo(s) peuvent encore etre envoyees`, 'info');
            }

            progressBar.style.display = 'block';
            let completed = 0;

            for (const file of toUpload) {
                const id = Date.now() + Math.random();
                addPreview(file, id);
                await uploadFile(file, id);
                completed++;
                progressFill.style.width = `${(completed / toUpload.length) * 100}%`;
            }

            setTimeout(() => {
                progressBar.style.display = 'none';
                progressFill.style.width = '0%';
            }, 1000);
        }

        cameraInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                handleFiles(e.target.files);
            }
            e.target.value = '';
        });

        galleryInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                handleFiles(e.target.files);
            }
            e.target.value = '';
        });
    </script>
</body>
</html>
