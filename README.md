# 3D Timber Illustration

![3D Timber Illustration Preview](images/three-cli.gif)

A lightweight Three.js-based technical illustration that renders a timber beam composition with measured dimensions, wood materials, axis references, and presentation-focused camera framing.

## Live Demo

https://test-three.raishannan.com

## Highlights

- Parametric modeling using configurable beam dimensions and offsets
- Technical visualization with dimension lines, markers, labels, and XYZ reference axes
- Occlusion-aware labels that hide automatically when blocked by geometry
- Modular scene structure for renderer, materials, and annotation logic
- Static deployment support via Docker and Nginx

## Visual Pipeline

1. Input dimensions define the beam sizes and offsets.
2. Geometry is generated for the three timber blocks.
3. Wood materials are applied using texture and normal maps.
4. Dimension annotations are added to key measurement points.
5. Camera, lighting, shadows, and axes complete the presentation layer.

## Local Preview

The project is a static frontend and can be opened through a local HTTP server.

Using Node:

```bash
npx serve .
```

Using Python:

```bash
python -m http.server 3000
```

Then open:

```text
http://localhost:3000
```

## Reference Target

![Expected Result](images/expected-result.jpg)
