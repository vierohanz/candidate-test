import * as THREE from 'three';

export class WoodMaterials {
    constructor() {
        this.loader = new THREE.TextureLoader();
    }

    async loadMaps() {
        try {
            const [map, normalMap] = await Promise.all([
                this.loadTexture('model/wood/wood.fbm/Colormap.png', THREE.SRGBColorSpace),
                this.loadTexture('model/wood/wood.fbm/NormalMap.png'),
            ]);
            return { map, normalMap };
        } catch {
            const fb = new THREE.DataTexture(new Uint8Array([160, 137, 96, 255]), 1, 1);
            fb.colorSpace = THREE.SRGBColorSpace; fb.needsUpdate = true;
            const fn = new THREE.DataTexture(new Uint8Array([128, 128, 255, 255]), 1, 1);
            fn.needsUpdate = true;
            return { map: fb, normalMap: fn };
        }
    }

    loadTexture(url, colorSpace = null) {
        return new Promise((resolve, reject) => {
            this.loader.load(url, tex => { 
                if (colorSpace) tex.colorSpace = colorSpace; 
                resolve(tex); 
            }, undefined, reject);
        });
    }

    createWoodMats(maps, w, h, d) {
        const g = 4.5;
        const face = (rx, ry, rot = 0, color = 0xffffff) => {
            const map = maps.map.clone(); map.needsUpdate = true;
            map.wrapS = map.wrapT = THREE.RepeatWrapping;
            map.repeat.set(rx, ry); map.center.set(0.5, 0.5); map.rotation = rot;
            
            const nrm = maps.normalMap.clone(); nrm.needsUpdate = true;
            nrm.wrapS = nrm.wrapT = THREE.RepeatWrapping;
            nrm.repeat.set(rx, ry); nrm.center.set(0.5, 0.5); nrm.rotation = rot;

            return new THREE.MeshStandardMaterial({ color, map, normalMap: nrm, roughness: 0.74, metalness: 0.02 });
        };
        return [
            face(d*g, h*g*0.8), face(d*g, h*g*0.8),
            face(d*g, w*g, Math.PI/2, 0xf6ead1), face(d*g, w*g, Math.PI/2, 0x8c7246),
            face(w*g, h*g*0.8), face(w*g, h*g*0.8),
        ];
    }

    createBlock(w, h, d, maps) {
        return new THREE.Mesh(new THREE.BoxGeometry(w, h, d), this.createWoodMats(maps, w, h, d));
    }
}
