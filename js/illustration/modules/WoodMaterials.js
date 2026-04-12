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

    createWoodMats(maps, w, h, d, posX = 0, posZ = 0) {
        const g = 1.6; 
        const x0 = posX - w / 2;
        const z0 = posZ - d / 2;
        
        const face = (width, len, color = 0xffffff, ox = 0, oy = 0) => {
            const map = maps.map.clone();
            map.wrapS = map.wrapT = THREE.RepeatWrapping;
            map.repeat.set(width * g, len * g); 
            map.center.set(0.5, 0.5);
            map.rotation = Math.PI / 2; 
            map.offset.set(ox % 1, oy % 1);
            
            const nrm = maps.normalMap.clone();
            nrm.wrapS = nrm.wrapT = THREE.RepeatWrapping;
            nrm.repeat.set(width * g, len * g);
            nrm.center.set(0.5, 0.5);
            nrm.rotation = Math.PI / 2;
            nrm.offset.set(ox % 1, oy % 1);

            return new THREE.MeshStandardMaterial({
                color,
                map,
                normalMap: nrm,
                normalScale: new THREE.Vector2(0.35, 0.35),
                roughness: 0.7,
                metalness: 0.05,
            });
        };

        const end = (width, height, color = 0xffffff, ox = 0, oy = 0) => {
            const map = maps.map.clone();
            map.wrapS = map.wrapT = THREE.RepeatWrapping;
            map.repeat.set(width * g, height * g);
            map.offset.set(ox % 1, oy % 1);
            return new THREE.MeshStandardMaterial({ color, map, roughness: 0.8 });
        };

        return [
            face(h, d, 0xffffff, 0, z0 * g),      // +X (Side)
            face(h, d, 0xffffff, 0, z0 * g),      // -X (Side)
            face(w, d, 0xf6ead1, x0 * g, z0 * g), // +Y (Top)
            face(w, d, 0x8c7246, x0 * g, z0 * g), // -Y (Bottom)
            end(w, h, 0xffffff, x0 * g, 0),      // +Z (End)
            end(w, h, 0xffffff, x0 * g, 0),      // -Z (End)
        ];
    }

    createBlock(w, h, d, maps, x = 0, y = 0, z = 0) {
        const mesh = new THREE.Mesh(new THREE.BoxGeometry(w, h, d), this.createWoodMats(maps, w, h, d, x, z));
        mesh.position.set(x, y, z);
        mesh.castShadow = true;
        mesh.receiveShadow = true;
        return mesh;
    }
}
