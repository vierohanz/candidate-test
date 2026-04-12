import * as THREE from 'three';
import { OrbitControls } from 'three/addons/controls/OrbitControls.js';

const container = document.getElementById('illustration-3d-container');
document.body.style.margin = '0';
document.body.style.overflow = 'hidden';
container.style.width = '100vw';
container.style.height = '100vh';

const scene = new THREE.Scene();
scene.background = new THREE.Color(0x000000);

const camera = new THREE.PerspectiveCamera(33, window.innerWidth / window.innerHeight, 0.1, 100);
camera.position.set(3.9, 2.7, 4.6);

const renderer = new THREE.WebGLRenderer({ antialias: true });
renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
renderer.setSize(window.innerWidth, window.innerHeight);
renderer.outputColorSpace = THREE.SRGBColorSpace;
container.appendChild(renderer.domElement);

const controls = new OrbitControls(camera, renderer.domElement);
controls.enableDamping = true;
controls.enablePan = false;
controls.minDistance = 2;
controls.maxDistance = 14;

scene.add(new THREE.AmbientLight(0xffffff, 1.7));
const keyLight = new THREE.DirectionalLight(0xffffff, 1.8);
keyLight.position.set(5, 6, 4);
scene.add(keyLight);
const fillLight = new THREE.DirectionalLight(0xfff7df, 0.8);
fillLight.position.set(-4, 2, -5);
scene.add(fillLight);
const axisGroup = new THREE.Group();
axisGroup.add(new THREE.AxesHelper(50));

const negativeZAxis = new THREE.Line(
    new THREE.BufferGeometry().setFromPoints([
        new THREE.Vector3(0, 0, 0),
        new THREE.Vector3(0, 0, -50),
    ]),
    new THREE.LineBasicMaterial({ color: 0x0000ff, toneMapped: false })
);
axisGroup.add(negativeZAxis);

const negativeXAxis = new THREE.Line(
    new THREE.BufferGeometry().setFromPoints([
        new THREE.Vector3(0, 0, 0),
        new THREE.Vector3(-50, 0, 0),
    ]),
    new THREE.LineBasicMaterial({ color: 0xff0000, toneMapped: false })
);
axisGroup.add(negativeXAxis);

const negativeYAxis = new THREE.Line(
    new THREE.BufferGeometry().setFromPoints([
        new THREE.Vector3(0, 0, 0),
        new THREE.Vector3(0, -50, 0),
    ]),
    new THREE.LineBasicMaterial({ color: 0x00ff00, toneMapped: false })
);
axisGroup.add(negativeYAxis);
scene.add(axisGroup);

const illustration = new THREE.Group();
scene.add(illustration);
const textureLoader = new THREE.TextureLoader();
const dimensionLabels = [];
const occluderMeshes = [];
const labelRaycaster = new THREE.Raycaster();
const labelWorldPosition = new THREE.Vector3();
const cameraToLabel = new THREE.Vector3();


const D = {
    h:    0.2,
    b1W:  0.5,  
    b1L:  2, 
    b2W:  0.5,  
    b2L:  2.54, 
    b3W:  0.5,  
    b3L:  2,    
    b3H:  0.2,  
    b3In: 0,    
    b3Back: -0.2, 
};

const xJ  = D.b1W;
const xR  = D.b1W + D.b2W;
const b3CX = xJ;
const b3X1 = b3CX - D.b3W / 2;
const b3X2 = b3CX + D.b3W / 2;
const tY   = D.h + D.b3H;

function createLabelSprite(text) {
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
    sprite.scale.set(0.28, 0.28, 0.28);
    return sprite;
}

function createMarker(pos) {
    const m = new THREE.Mesh(
        new THREE.BoxGeometry(0.045, 0.045, 0.045),
        new THREE.MeshBasicMaterial({ color: 0xffffff, toneMapped: false })
    );
    m.position.copy(pos);
    m.rotation.set(Math.PI / 4, Math.PI / 4, 0);
    return m;
}

function addDimensionLine(group, start, end, label) {
    const line = new THREE.Line(
        new THREE.BufferGeometry().setFromPoints([start, end]),
        new THREE.LineBasicMaterial({ color: 0xffffff, toneMapped: false })
    );
    const text = createLabelSprite(label);
    text.position.copy(start).lerp(end, 0.5);
    dimensionLabels.push({ sprite: text, start: start.clone(), end: end.clone() });
    group.add(line, createMarker(start), createMarker(end), text);
}

function addOutline(mesh, group, excludeLocalX = null) {
    const srcEdges = new THREE.EdgesGeometry(mesh.geometry);
    let geo;
    if (excludeLocalX !== null) {
        const src = srcEdges.attributes.position.array;
        const kept = [];
        for (let i = 0; i < src.length; i += 6) {
            if (Math.abs(src[i] - excludeLocalX) < 0.001 && Math.abs(src[i+3] - excludeLocalX) < 0.001) continue;
            kept.push(src[i], src[i+1], src[i+2], src[i+3], src[i+4], src[i+5]);
        }
        geo = new THREE.BufferGeometry();
        geo.setAttribute('position', new THREE.Float32BufferAttribute(kept, 3));
    } else {
        geo = srcEdges;
    }
    const lines = new THREE.LineSegments(
        geo,
        new THREE.LineBasicMaterial({ color: 0xffffff, transparent: true, opacity: 0.95, toneMapped: false })
    );
    lines.position.copy(mesh.position);
    lines.rotation.copy(mesh.rotation);
    lines.scale.copy(mesh.scale);
    group.add(lines);
}

function setTextureRepeat(tex, x, y, rot = 0) {
    tex.wrapS = tex.wrapT = THREE.RepeatWrapping;
    tex.repeat.set(x, y); tex.center.set(0.5, 0.5);
    tex.rotation = rot;
    tex.anisotropy = renderer.capabilities.getMaxAnisotropy();
}

function loadTexture(url, colorSpace = null) {
    return new Promise((resolve, reject) => {
        textureLoader.load(url, tex => { if (colorSpace) tex.colorSpace = colorSpace; resolve(tex); }, undefined, reject);
    });
}

function createWoodMats(maps, w, h, d) {
    const g = 4.5;
    const face = (rx, ry, rot = 0, color = 0xffffff) => {
        const map = maps.map.clone(); map.needsUpdate = true; setTextureRepeat(map, rx, ry, rot);
        const nrm = maps.normalMap.clone(); nrm.needsUpdate = true; setTextureRepeat(nrm, rx, ry, rot);
        return new THREE.MeshStandardMaterial({ color, map, normalMap: nrm, roughness: 0.74, metalness: 0.02 });
    };
    return [
        face(d*g, h*g*0.8), face(d*g, h*g*0.8),
        face(d*g, w*g, Math.PI/2, 0xf6ead1), face(d*g, w*g, Math.PI/2, 0x8c7246),
        face(w*g, h*g*0.8), face(w*g, h*g*0.8),
    ];
}

function createBlock(w, h, d, maps) {
    return new THREE.Mesh(new THREE.BoxGeometry(w, h, d), createWoodMats(maps, w, h, d));
}

const b3Z1 = D.b3Back;
const b3Z2 = D.b3Back + D.b3L;
const b3CZ = (b3Z1 + b3Z2) / 2;

function createMeshes(maps) {
    const { h, b1W, b1L, b2W, b2L, b3W, b3L, b3H } = D;

    const beam1 = createBlock(b1W, h, b1L, maps);
    beam1.position.set(b1W / 2, h / 2, b1L / 2);

    const beam2 = createBlock(b2W, h, b2L, maps);
    beam2.position.set(xJ + b2W / 2, h / 2, b2L / 2);

    const beam3 = createBlock(b3W, b3H, b3L, maps);
    beam3.position.set(b3CX, h + b3H / 2, b3CZ);

    return [beam1, beam2, beam3];
}

function addDimensions(group) {
    const { h, b1L, b2L, b3L } = D;
    const v = (...a) => new THREE.Vector3(...a);
    const add = (s, e, lbl) => addDimensionLine(group, v(...s), v(...e), lbl);

    add([0,    0,  0], [0,    0,  b1L], '2m');      
    add([xR,   h,  0], [xR,   h,  b2L], '3m');      
    add([b3X1, tY, b3Z1], [b3X1, tY, b3Z2], '2m');      
    add([0,    0, 0], [xJ,   0, 0], '0.5m');         
    add([xJ,   0, 0], [xR,   0, 0], '0.5m');         
    add([b3X1, h, b3Z1], [b3X2, h, b3Z1], '0.5m');         

    add([0,    0, 0], [0,    h,   0], '0.2m');        
    add([xR,   0, 0], [xR,   h,   0], '0.2m');        
    add([b3X2, h, b3Z1], [b3X2, tY,  b3Z1], '0.2m');        
    add([xJ - 0.13, h, b3Z2], [xJ - 0.13, h, b1L], '0.02m'); 
    add([0,  0, b1L], [xJ, 0, b1L], '0.5m');          
    add([xJ, 0, b1L], [xJ, 0, b2L], '0.54m');          

    add([b3X1, tY, b3Z2], [b3X2, tY, b3Z2], '0.5m');

    add([0,  0, b1L], [0,  h,  b1L], '0.2m');         
    add([xR, 0, b2L], [xR, h,  b2L], '0.2m');         
}
function buildIllustration(maps) {
    const [beam1, beam2, beam3] = createMeshes(maps);
    occluderMeshes.length = 0;

    illustration.add(beam1);
    occluderMeshes.push(beam1);
    addOutline(beam1, illustration, +D.b1W / 2);
    illustration.add(beam2);
    occluderMeshes.push(beam2);
    addOutline(beam2, illustration, -D.b2W / 2);

    illustration.add(beam3);
    occluderMeshes.push(beam3);
    addOutline(beam3, illustration);

    addDimensions(illustration);
}

async function loadWoodMaps() {
    try {
        const [map, normalMap] = await Promise.all([
            loadTexture('model/wood/wood.fbm/Colormap.png', THREE.SRGBColorSpace),
            loadTexture('model/wood/wood.fbm/NormalMap.png'),
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

function frameScene() {
    axisGroup.position.set(xR, 0, D.b2L);
    axisGroup.rotation.y = Math.PI / 2;
    controls.target.set(0.78, 0.22, 1.35);
    controls.update();
    camera.lookAt(controls.target);
}

function onWindowResize() {
    camera.aspect = window.innerWidth / window.innerHeight;
    camera.updateProjectionMatrix();
    renderer.setSize(window.innerWidth, window.innerHeight);
}
window.addEventListener('resize', onWindowResize);

function updateLabelRotations() {
    for (const { sprite, start, end } of dimensionLabels) {
        const s = start.clone().project(camera);
        const e = end.clone().project(camera);
        let angle = Math.atan2(e.y - s.y, e.x - s.x);
        if (angle > Math.PI / 2 || angle < -Math.PI / 2) angle += Math.PI;
        sprite.material.rotation = angle;
    }
}

function updateLabelVisibility() {
    for (const { sprite } of dimensionLabels) {
        sprite.getWorldPosition(labelWorldPosition);
        cameraToLabel.copy(labelWorldPosition).sub(camera.position);

        const labelDistance = cameraToLabel.length();
        if (labelDistance <= 0.001) {
            sprite.visible = false;
            continue;
        }

        labelRaycaster.set(camera.position, cameraToLabel.normalize());
        labelRaycaster.far = Math.max(labelDistance - 0.06, 0);

        sprite.visible = labelRaycaster.intersectObjects(occluderMeshes, false).length === 0;
    }
}

function animate() {
    controls.update();
    updateLabelRotations();
    updateLabelVisibility();
    renderer.render(scene, camera);
}

async function init() {
    const maps = await loadWoodMaps();
    buildIllustration(maps);
    illustration.rotation.y = 0;
    frameScene();
}

renderer.setAnimationLoop(animate);
init();
