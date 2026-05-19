// Visualizers and Audio Analysis - MUSICYOS

class AudioVisualizer {
    constructor() {
        this.audioContext = null;
        this.analyser = null;
        this.dataArray = null;
        this.bufferLength = 0;
        this.animationId = null;
        this.isPlaying = false;
        
        this.initVisualizers();
        this.initDemo();
    }
    
    initVisualizers() {
        const AudioContext = window.AudioContext || window.webkitAudioContext;
        if (!AudioContext) return;
        
        this.audioContext = new AudioContext();
        this.analyser = this.audioContext.createAnalyser();
        this.analyser.fftSize = 256;
        
        this.bufferLength = this.analyser.frequencyBinCount;
        this.dataArray = new Uint8Array(this.bufferLength);
        
        // Connecter les visualiseurs
        this.drawWaveformLeft();
        this.drawWaveformRight();
        this.drawSpectrum();
    }
    
    drawWaveformLeft() {
        const canvas = document.getElementById('waveformLeft');
        if (!canvas) return;
        
        const ctx = canvas.getContext('2d');
        
        const draw = () => {
            this.analyser.getByteFrequencyData(this.dataArray);
            
            ctx.fillStyle = 'rgba(0, 0, 0, 0.1)';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            
            ctx.strokeStyle = '#00d4ff';
            ctx.lineWidth = 2;
            ctx.shadowColor = 'rgba(0, 212, 255, 0.5)';
            ctx.shadowBlur = 10;
            
            ctx.beginPath();
            const sliceWidth = canvas.width / this.bufferLength;
            let x = 0;
            
            for (let i = 0; i < this.bufferLength; i++) {
                const v = this.dataArray[i] / 128.0;
                const y = (v * canvas.height) / 2;
                
                if (i === 0) {
                    ctx.moveTo(x, y);
                } else {
                    ctx.lineTo(x, y);
                }
                
                x += sliceWidth;
            }
            
            ctx.lineTo(canvas.width, canvas.height / 2);
            ctx.stroke();
            ctx.shadowColor = 'transparent';
            
            this.animationId = requestAnimationFrame(draw);
        };
        
        draw();
    }
    
    drawWaveformRight() {
        const canvas = document.getElementById('waveformRight');
        if (!canvas) return;
        
        const ctx = canvas.getContext('2d');
        
        const draw = () => {
            this.analyser.getByteFrequencyData(this.dataArray);
            
            ctx.fillStyle = 'rgba(0, 0, 0, 0.1)';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            
            ctx.strokeStyle = '#ff006e';
            ctx.lineWidth = 2;
            ctx.shadowColor = 'rgba(255, 0, 110, 0.5)';
            ctx.shadowBlur = 10;
            
            ctx.beginPath();
            const sliceWidth = canvas.width / this.bufferLength;
            let x = 0;
            
            for (let i = 0; i < this.bufferLength; i++) {
                const v = this.dataArray[i] / 128.0;
                const y = canvas.height - (v * canvas.height) / 2;
                
                if (i === 0) {
                    ctx.moveTo(x, y);
                } else {
                    ctx.lineTo(x, y);
                }
                
                x += sliceWidth;
            }
            
            ctx.lineTo(canvas.width, canvas.height / 2);
            ctx.stroke();
            ctx.shadowColor = 'transparent';
        };
        
        draw();
    }
    
    drawSpectrum() {
        const canvas = document.getElementById('spectrum');
        if (!canvas) return;
        
        const ctx = canvas.getContext('2d');
        
        const draw = () => {
            this.analyser.getByteFrequencyData(this.dataArray);
            
            ctx.fillStyle = 'rgba(0, 0, 0, 0.2)';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            
            const barWidth = (canvas.width / this.bufferLength) * 2.5;
            let x = 0;
            
            for (let i = 0; i < this.bufferLength; i++) {
                const barHeight = (this.dataArray[i] / 255) * canvas.height;
                
                // Gradient de couleur
                const hue = (i / this.bufferLength) * 360;
                ctx.fillStyle = `hsl(${hue}, 100%, 50%)`;
                ctx.shadowColor = `hsl(${hue}, 100%, 50%)`;
                ctx.shadowBlur = 10;
                
                ctx.fillRect(x, canvas.height - barHeight, barWidth, barHeight);
                
                x += barWidth + 1;
            }
            
            ctx.shadowColor = 'transparent';
        };
        
        draw();
    }
    
    initDemo() {
        const demoPlayBtn = document.getElementById('demoPlayBtn');
        const demoStopBtn = document.getElementById('demoStopBtn');
        const presetBtns = document.querySelectorAll('.demo-btn');
        const tempoSlider = document.getElementById('tempoSlider');
        const volumeSlider = document.getElementById('volumeSlider');
        
        if (demoPlayBtn) {
            demoPlayBtn.addEventListener('click', () => this.playDemo());
        }
        
        if (demoStopBtn) {
            demoStopBtn.addEventListener('click', () => this.stopDemo());
        }
        
        presetBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                presetBtns.forEach(b => b.classList.remove('active'));
                e.target.classList.add('active');
            });
        });
        
        if (tempoSlider) {
            tempoSlider.addEventListener('input', (e) => {
                document.getElementById('tempoValue').textContent = e.target.value;
            });
        }
        
        if (volumeSlider) {
            volumeSlider.addEventListener('input', (e) => {
                document.getElementById('volumePercentage').textContent = e.target.value;
            });
        }
    }
    
    playDemo() {
        if (this.isPlaying) return;
        
        this.isPlaying = true;
        const tempoSlider = document.getElementById('tempoSlider');
        const volumeSlider = document.getElementById('volumeSlider');
        const tempo = parseInt(tempoSlider.value);
        const volume = parseInt(volumeSlider.value) / 100;
        
        this.generateDemoAudio(tempo, volume);
    }
    
    stopDemo() {
        this.isPlaying = false;
    }
    
    generateDemoAudio(tempo, volume) {
        if (!this.audioContext) {
            const AudioContext = window.AudioContext || window.webkitAudioContext;
            this.audioContext = new AudioContext();
        }
        
        // Créer un oscillateur pour la démo
        const osc = this.audioContext.createOscillator();
        const gain = this.audioContext.createGain();
        
        osc.connect(gain);
        gain.connect(this.audioContext.destination);
        gain.connect(this.analyser);
        
        // Paramètres de base
        osc.type = 'sine';
        gain.gain.setValueAtTime(volume * 0.1, this.audioContext.currentTime);
        
        // Mélody simple
        const frequencies = [440, 494, 523, 587]; // A, B, C, D
        const noteDuration = (60 / tempo) / 4;
        
        osc.start(this.audioContext.currentTime);
        
        let time = this.audioContext.currentTime;
        let noteIndex = 0;
        
        const playNote = () => {
            if (!this.isPlaying) {
                osc.stop();
                return;
            }
            
            osc.frequency.setTargetAtTime(
                frequencies[noteIndex % frequencies.length],
                time,
                0.05
            );
            
            time += noteDuration;
            noteIndex++;
            
            setTimeout(playNote, noteDuration * 1000);
        };
        
        playNote();
    }
    
    drawDemoVisualizer() {
        const canvas = document.getElementById('demoCanvas');
        if (!canvas) return;
        
        const ctx = canvas.getContext('2d');
        
        const draw = () => {
            this.analyser.getByteFrequencyData(this.dataArray);
            
            ctx.fillStyle = 'rgba(0, 0, 0, 0.1)';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            
            ctx.strokeStyle = '#00d4ff';
            ctx.lineWidth = 3;
            ctx.shadowColor = 'rgba(0, 212, 255, 0.8)';
            ctx.shadowBlur = 20;
            
            ctx.beginPath();
            const sliceWidth = canvas.width / this.bufferLength;
            let x = 0;
            
            for (let i = 0; i < this.bufferLength; i++) {
                const v = this.dataArray[i] / 128.0;
                const y = (v * canvas.height) / 2;
                
                if (i === 0) {
                    ctx.moveTo(x, y);
                } else {
                    ctx.lineTo(x, y);
                }
                
                x += sliceWidth;
            }
            
            ctx.lineTo(canvas.width, canvas.height / 2);
            ctx.stroke();
            
            if (this.isPlaying) {
                requestAnimationFrame(draw);
            }
        };
        
        draw();
    }
}

// Initialiser les visualiseurs au chargement
document.addEventListener('DOMContentLoaded', () => {
    new AudioVisualizer();
});
