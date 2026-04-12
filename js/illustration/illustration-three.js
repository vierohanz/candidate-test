import * as THREE from 'three';
import { SceneCore } from './modules/SceneCore.js';
import { DimensionSystem } from './modules/DimensionSystem.js';
import { WoodMaterials } from './modules/WoodMaterials.js';

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
const b3Z1 = D.b3Back;
const b3Z2 = D.b3Back + D.b3L;
const b3CZ = (D.b3Back + (D.b3Back + D.b3L)) / 2;

const core = new SceneCore('illustration-3d-container');
const dims = new DimensionSystem(core.scene, core.camera);
const wood = new WoodMaterials();
const splashScreen = document.getElementById('splash-screen');
const splashStatusText = document.getElementById('splash-status-text');

function updateSplashStatus(text) {
    if (splashStatusText) {
        splashStatusText.textContent = text;
    }
}

function hideSplashScreen() {
    if (!splashScreen) return;

    splashScreen.classList.add('is-hidden');
    window.setTimeout(() => {
        splashScreen.remove();
    }, 800);
}

async function init() {
    updateSplashStatus('Loading wood textures');
    const maps = await wood.loadMaps();
    updateSplashStatus('Building geometry');
    const illustration = new THREE.Group();
    core.scene.add(illustration);

    const beam1 = wood.createBlock(D.b1W, D.h, D.b1L, maps);
    beam1.position.set(D.b1W / 2, D.h / 2, D.b1L / 2);
    illustration.add(beam1);

    const beam2 = wood.createBlock(D.b2W, D.h, D.b2L, maps);
    beam2.position.set(xJ + D.b2W / 2, D.h / 2, D.b2L / 2);
    illustration.add(beam2);

    const beam3 = wood.createBlock(D.b3W, D.b3H, D.b3L, maps);
    beam3.position.set(b3CX, D.h + D.b3H / 2, b3CZ);
    illustration.add(beam3);

    dims.occluders.push(beam1, beam2, beam3);

    addDimensions(illustration);
    frameScene();
    updateSplashStatus('Finalizing presentation');
    
    core.onAnimate = () => {
        dims.update();
    };
    core.start();
    window.setTimeout(hideSplashScreen, 850);
}

function addDimensions(group) {
    const { h, b1L, b2L, b3L } = D;
    const v = (x, y, z) => new THREE.Vector3(x, y, z);

    dims.addDimension(v(0,  0, 0), v(0,  0, b1L), '2m', group);      
    dims.addDimension(v(xR, h, 0), v(xR, h, b2L), '3m', group);      
    dims.addDimension(v(b3X1, tY, b3Z1), v(b3X1, tY, b3Z2), '2m', group);      
    
    dims.addDimension(v(0,  0, 0), v(xJ, 0, 0), '0.5m', group);         
    dims.addDimension(v(xJ, 0, 0), v(xR, 0, 0), '0.5m', group);         
    dims.addDimension(v(b3X1, h, b3Z1), v(b3X2, h, b3Z1), '0.5m', group);         

    dims.addDimension(v(0,  0, 0), v(0,  h, 0), '0.2m', group);        
    dims.addDimension(v(xR, 0, 0), v(xR, h, 0), '0.2m', group);        
    dims.addDimension(v(b3X2, h, b3Z1), v(b3X2, tY, b3Z1), '0.2m', group);        

    dims.addDimension(v(xJ - 0.13, h, b3Z2), v(xJ - 0.13, h, b1L), '0.02m', group); 
    dims.addDimension(v(0,  0, b1L), v(xJ, 0, b1L), '0.5m', group);          
    dims.addDimension(v(xJ, 0, b1L), v(xJ, 0, b2L), '0.54m', group);          
    dims.addDimension(v(xJ, 0, b2L), v(xR, 0, b2L), '0.5m', group);          

    dims.addDimension(v(b3X1, tY, b3Z2), v(b3X2, tY, b3Z2), '0.5m', group);
    dims.addDimension(v(0,  0, b1L), v(0,  h, b1L), '0.2m', group);         
    dims.addDimension(v(xR, 0, b2L), v(xR, h, b2L), '0.2m', group);         
}

function frameScene() {
    if (core.axisGroup) {
        core.axisGroup.position.set(xR, 0, D.b2L);
        core.axisGroup.rotation.y = Math.PI / 2;
    }

    const focusTarget = new THREE.Vector3(0.78, 0.22, 1.35);
    const finalCameraPosition = new THREE.Vector3(3.1, 2.3, 3.2);
    core.camera.position.set(7.5, 5.8, 7.8);
    core.controls.target.set(1.5, 0.5, 1.9);
    core.animateCameraTo(finalCameraPosition, focusTarget, 1800);
    core.controls.update();
}

init();
