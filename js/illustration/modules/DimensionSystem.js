import * as THREE from 'three';

export class DimensionSystem {
    constructor(scene, camera) {
        this.scene = scene;
        this.camera = camera;
        this.labels = [];
        this.occluders = [];
        this.raycaster = new THREE.Raycaster();
        this.tempPos = new THREE.Vector3();
    }

    createLabelSprite(text) {
        const canvas = document.createElement('canvas');
        canvas.width = 256; canvas.height = 256;
        const ctx = canvas.getContext('2d');
        ctx.clearRect(0, 0, 256, 256);
        ctx.font = '300 42px Arial';
        ctx.textAlign = 'center'; ctx.textBaseline = 'middle';
        ctx.lineJoin = 'round'; ctx.lineWidth = 6;
        ctx.strokeStyle = '#0d0d0d'; ctx.fillStyle = '#f1f1f1';
        ctx.strokeText(text, 128, 128); ctx.fillText(text, 128, 128);
        const tex = new THREE.CanvasTexture(canvas);
        tex.colorSpace = THREE.SRGBColorSpace;
        const mat = new THREE.SpriteMaterial({ map: tex, transparent: true, depthTest: false, depthWrite: false });
        const sprite = new THREE.Sprite(mat);
        sprite.scale.set(0.18, 0.18, 0.18);
        return sprite;
    }

    createMarker(pos) {
        const m = new THREE.Mesh(
            new THREE.BoxGeometry(0.012, 0.012, 0.012),
            new THREE.MeshBasicMaterial({ color: 0xffffff, toneMapped: false })
        );
        m.position.copy(pos);
        m.rotation.set(Math.PI / 4, Math.PI / 4, 0);
        return m;
    }

    addDimension(start, end, text, group) {
        const dir = new THREE.Vector3().subVectors(end, start);
        const len = dir.length();
        const line = new THREE.Mesh(
            new THREE.BoxGeometry(0.005, 0.005, len),
            new THREE.MeshBasicMaterial({ color: 0xffffff, toneMapped: false })
        );
        line.position.copy(start).add(dir.clone().multiplyScalar(0.5));
        line.lookAt(end);

        const label = this.createLabelSprite(text);
        label.position.copy(start).lerp(end, 0.5);
        
        this.labels.push({ sprite: label, start: start.clone(), end: end.clone() });
        group.add(line, this.createMarker(start), this.createMarker(end), label);
    }

    update() {
        for (const data of this.labels) {
            // Update rotations
            const s = data.start.clone().project(this.camera);
            const e = data.end.clone().project(this.camera);
            let angle = Math.atan2(e.y - s.y, e.x - s.x);
            if (angle > Math.PI / 2 || angle < -Math.PI / 2) angle += Math.PI;
            data.sprite.material.rotation = angle;

            // Visibility (occlusion)
            data.sprite.getWorldPosition(this.tempPos);
            const camToLabel = this.tempPos.clone().sub(this.camera.position);
            const dist = camToLabel.length();
            
            if (dist > 0.001) {
                this.raycaster.set(this.camera.position, camToLabel.normalize());
                this.raycaster.far = Math.max(dist - 0.06, 0);
                data.sprite.visible = this.raycaster.intersectObjects(this.occluders, false).length === 0;
            }
        }
    }
}
