@props([
    'action',
    'field',
    'label',
    'placeholder',
    'button',
])

@php($scannerId = 'scanner_'.str_replace(['.', '-'], '_', uniqid()))

<form method="post" action="{{ $action }}" class="mt-6 rounded-lg border bg-white p-6" data-qr-scanner="{{ $scannerId }}">
    @csrf

    <div class="rounded-lg border border-neutral-200 bg-neutral-950 p-3">
        <video class="hidden aspect-video w-full rounded-md bg-black object-cover" playsinline muted></video>
        <div class="grid aspect-video place-items-center rounded-md bg-neutral-900 text-center text-sm font-bold text-neutral-300" data-camera-placeholder>
            Kamera scanner belum aktif
        </div>
    </div>

    <div class="mt-4 flex flex-wrap gap-2">
        <button type="button" class="rounded-full bg-neutral-950 px-5 py-2 text-sm font-bold text-white" data-start-scan>Mulai Scan</button>
        <button type="button" class="rounded-full border border-neutral-300 px-5 py-2 text-sm font-bold" data-stop-scan>Stop</button>
    </div>

    <p class="mt-3 text-sm text-neutral-600" data-scan-status>Scan QR lewat kamera atau masukkan kode manual.</p>

    <label class="mt-5 block text-sm font-bold">{{ $label }}</label>
    <input name="{{ $field }}" class="mt-2 w-full rounded-md border px-4 py-3 font-mono" placeholder="{{ $placeholder }}" autofocus required data-code-input>

    <button class="mt-5 w-full rounded-full bg-neutral-950 px-5 py-3 font-bold text-white">{{ $button }}</button>
</form>

<script>
(() => {
    const form = document.querySelector('[data-qr-scanner="{{ $scannerId }}"]');
    if (!form) return;

    const video = form.querySelector('video');
    const placeholder = form.querySelector('[data-camera-placeholder]');
    const status = form.querySelector('[data-scan-status]');
    const input = form.querySelector('[data-code-input]');
    const startButton = form.querySelector('[data-start-scan]');
    const stopButton = form.querySelector('[data-stop-scan]');
    let stream = null;
    let scanning = false;
    let detector = null;

    const stop = () => {
        scanning = false;
        if (stream) {
            stream.getTracks().forEach((track) => track.stop());
            stream = null;
        }
        video.srcObject = null;
        video.classList.add('hidden');
        placeholder.classList.remove('hidden');
        status.textContent = 'Scanner berhenti. Kode masih bisa dimasukkan manual.';
    };

    const scanLoop = async () => {
        if (!scanning || !detector) return;

        try {
            const codes = await detector.detect(video);
            if (codes.length > 0) {
                const value = codes[0].rawValue || '';
                if (value.trim() !== '') {
                    input.value = value.trim();
                    status.textContent = 'QR terbaca. Memproses kode...';
                    stop();
                    form.submit();
                    return;
                }
            }
        } catch (error) {
            status.textContent = 'Scanner belum menemukan QR. Arahkan kamera ke kode.';
        }

        requestAnimationFrame(scanLoop);
    };

    startButton.addEventListener('click', async () => {
        if (!('BarcodeDetector' in window)) {
            status.textContent = 'Browser belum mendukung scan kamera. Masukkan kode manual.';
            return;
        }

        try {
            detector = new BarcodeDetector({ formats: ['qr_code'] });
            stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
            video.srcObject = stream;
            await video.play();
            scanning = true;
            video.classList.remove('hidden');
            placeholder.classList.add('hidden');
            status.textContent = 'Scanner aktif. Arahkan kamera ke QR Code.';
            requestAnimationFrame(scanLoop);
        } catch (error) {
            status.textContent = 'Kamera tidak bisa dibuka. Cek izin kamera atau masukkan kode manual.';
        }
    });

    stopButton.addEventListener('click', stop);
})();
</script>
