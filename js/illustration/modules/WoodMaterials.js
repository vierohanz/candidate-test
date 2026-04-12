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
        const g = 1.35; // Lower density for a more premium look
        const x0 = posX - w / 2;
        const z0 = posZ - d / 2;
        
        const createMat = (rx, ry, rot, color, ox, oy) => {
            const map = maps.map.clone();
            map.wrapS = map.wrapT = THREE.RepeatWrapping;
            map.repeat.set(rx * g, ry * g);
            map.rotation = rot;
            map.center.set(0.5, 0.5);
            map.offset.set(ox % 1, oy % 1);
            
            const nrm = maps.normalMap.clone();
            nrm.wrapS = nrm.wrapT = THREE.RepeatWrapping;
            nrm.repeat.set(rx * g, ry * g);
            nrm.rotation = rot;
            nrm.center.set(0.5, 0.5);
            nrm.offset.set(ox % 1, oy % 1);

            return new THREE.MeshStandardMaterial({
                color,
                map,
                normalMap: nrm,
                normalScale: new THREE.Vector2(0.32, 0.32),
                roughness: 0.75,
                metalness: 0.04,
            });
        };

        return [
            createMat(d, h, 0, 0xffffff, z0 * g, 0),               
            createMat(d, h, 0, 0xffffff, z0 * g, 0),               
            
            createMat(w, d, Math.PI / 2, 0xf1e7d2, x0 * g, z0 * g), 
            createMat(w, d, Math.PI / 2, 0x937d63, x0 * g, z0 * g), 
            
            createMat(w, h, 0, 0xffffff, x0 * g, 0),               
            createMat(w, h, 0, 0xffffff, x0 * g, 0),               
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
