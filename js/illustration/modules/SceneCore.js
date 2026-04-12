import * as THREE from 'three';
import { OrbitControls } from 'three/addons/controls/OrbitControls.js';

export class SceneCore {
    constructor(containerId) {
        this.container = document.getElementById(containerId);
        this.scene = new THREE.Scene();
        this.camera = new THREE.PerspectiveCamera(45, window.innerWidth / window.innerHeight, 0.1, 1000);
        this.renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
        this.controls = null;
        this.onAnimate = null;

        this.init();
    }

    init() {
        this.renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
        this.renderer.setSize(window.innerWidth, window.innerHeight);
        this.renderer.setClearColor(0x000000, 1);
        this.container.appendChild(this.renderer.domElement);

        this.controls = new OrbitControls(this.camera, this.renderer.domElement);
        this.controls.enableDamping = true;
        this.controls.enablePan = false;
        this.controls.minDistance = 2;
        this.controls.maxDistance = 14;

        this.addAxes();
        this.addLights();
        window.addEventListener('resize', () => this.onResize());
    }

    addAxes() {
        const axisGroup = new THREE.Group();
        axisGroup.add(new THREE.AxesHelper(50));

        const createNegAxis = (dir, color) => {
            return new THREE.Line(
                new THREE.BufferGeometry().setFromPoints([new THREE.Vector3(0, 0, 0), dir]),
                new THREE.LineBasicMaterial({ color, toneMapped: false })
            );
        };

        axisGroup.add(createNegAxis(new THREE.Vector3(0, 0, -50), 0x0000ff));
        axisGroup.add(createNegAxis(new THREE.Vector3(-50, 0, 0), 0xff0000));
        axisGroup.add(createNegAxis(new THREE.Vector3(0, -50, 0), 0x00ff00));
        
        this.axisGroup = axisGroup;
        this.scene.add(axisGroup);
    }

    addLights() {
        this.scene.add(new THREE.AmbientLight(0xffffff, 1.7));
        const keyLight = new THREE.DirectionalLight(0xffffff, 1.8);
        keyLight.position.set(5, 6, 4);
        this.scene.add(keyLight);
        const fillLight = new THREE.DirectionalLight(0xfff7df, 0.8);
        fillLight.position.set(-4, 2, -5);
        this.scene.add(fillLight);
    }

    onResize() {
        this.camera.aspect = window.innerWidth / window.innerHeight;
        this.camera.updateProjectionMatrix();
        this.renderer.setSize(window.innerWidth, window.innerHeight);
    }

    render() {
        if (this.onAnimate) this.onAnimate();
        this.controls.update();
        this.renderer.render(this.scene, this.camera);
    }

    start() {
        this.renderer.setAnimationLoop(() => this.render());
    }
}
