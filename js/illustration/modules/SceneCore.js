import * as THREE from 'three';
import { OrbitControls } from 'three/addons/controls/OrbitControls.js';

export class SceneCore {
    constructor(containerId) {
        this.container = document.getElementById(containerId);
        this.scene = new THREE.Scene();
        this.camera = new THREE.PerspectiveCamera(45, window.innerWidth / window.innerHeight, 0.1, 1000);
        this.renderer = new THREE.WebGLRenderer({
            antialias: true,
            alpha: true,
            powerPreference: 'high-performance',
        });
        this.controls = null;
        this.onAnimate = null;
        this.keyLight = null;
        this.fillLight = null;
        this.rimLight = null;
        this.shadowCatcher = null;

        this.init();
    }

    init() {
        this.renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
        this.renderer.setSize(window.innerWidth, window.innerHeight);
        this.renderer.setClearColor(0x000000, 1);
        this.renderer.outputColorSpace = THREE.SRGBColorSpace;
        this.renderer.toneMapping = THREE.ACESFilmicToneMapping;
        this.renderer.toneMappingExposure = 1.12;
        this.renderer.shadowMap.enabled = true;
        this.renderer.shadowMap.type = THREE.PCFSoftShadowMap;
        this.container.appendChild(this.renderer.domElement);

        this.controls = new OrbitControls(this.camera, this.renderer.domElement);
        this.controls.enableDamping = true;
        this.controls.enablePan = false;
        this.controls.minDistance = 2;
        this.controls.maxDistance = 14;
        this.controls.minPolarAngle = 0.45;
        this.controls.maxPolarAngle = Math.PI / 2.05;

        this.addAxes();
        this.addLights();
        this.addShadowCatcher();
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
        this.scene.add(new THREE.AmbientLight(0xffffff, 1.15));

        this.keyLight = new THREE.DirectionalLight(0xfff6e4, 2.6);
        this.keyLight.position.set(5, 7, 4);
        this.keyLight.castShadow = true;
        this.keyLight.shadow.mapSize.set(2048, 2048);
        this.keyLight.shadow.bias = -0.00015;
        this.keyLight.shadow.normalBias = 0.02;
        this.keyLight.shadow.camera.near = 0.5;
        this.keyLight.shadow.camera.far = 20;
        this.keyLight.shadow.camera.left = -6;
        this.keyLight.shadow.camera.right = 6;
        this.keyLight.shadow.camera.top = 6;
        this.keyLight.shadow.camera.bottom = -6;
        this.scene.add(this.keyLight);

        this.fillLight = new THREE.DirectionalLight(0xe7f2ff, 0.7);
        this.fillLight.position.set(-4, 3, -5);
        this.scene.add(this.fillLight);

        this.rimLight = new THREE.DirectionalLight(0xffe1b0, 0.55);
        this.rimLight.position.set(-2, 5, 6);
        this.scene.add(this.rimLight);
    }

    addShadowCatcher() {
        const plane = new THREE.Mesh(
            new THREE.PlaneGeometry(30, 30),
            new THREE.ShadowMaterial({ color: 0x000000, opacity: 0.22 })
        );
        plane.rotation.x = -Math.PI / 2;
        plane.position.y = -0.001;
        plane.receiveShadow = true;
        this.shadowCatcher = plane;
        this.scene.add(plane);
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
