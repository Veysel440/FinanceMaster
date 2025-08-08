import React, { useCallback, useMemo, useState } from "react";
import Cropper from "react-easy-crop";
import type { Area } from "react-easy-crop";
import { getCroppedBlob } from "../utils/cropImage";

type Props = {
    file: File;
    onCancel: () => void;
    onCropped: (blob: Blob) => void;
};

const AvatarCropper: React.FC<Props> = ({ file, onCancel, onCropped }) => {
    const [crop, setCrop] = useState<{ x: number; y: number }>({ x: 0, y: 0 });
    const [zoom, setZoom] = useState<number>(1.2);
    const [croppedPixels, setCroppedPixels] = useState<Area | null>(null);

    const objectUrl = useMemo(() => URL.createObjectURL(file), [file]);

    const onCropComplete = useCallback(
        (_croppedArea: Area, croppedAreaPixels: Area) => {
            setCroppedPixels(croppedAreaPixels);
        },
        []
    );

    const handleDone = useCallback(async () => {
        if (!croppedPixels) return;
        try {
            const blob = await getCroppedBlob(objectUrl, croppedPixels);
            onCropped(blob);
        } finally {
            URL.revokeObjectURL(objectUrl);
        }
    }, [croppedPixels, objectUrl, onCropped]);

    const handleCancel = useCallback(() => {
        URL.revokeObjectURL(objectUrl);
        onCancel();
    }, [objectUrl, onCancel]);

    return (
        <div className="fixed inset-0 bg-black/50 z-50 flex items-center justify-center">
            <div className="bg-white dark:bg-gray-900 p-4 rounded w-[90vw] max-w-md">
                <div className="relative w-full h-64">
                    <Cropper
                        image={objectUrl}
                        crop={crop}
                        zoom={zoom}
                        aspect={1}
                        onCropChange={setCrop}
                        onZoomChange={setZoom}
                        onCropComplete={onCropComplete}
                        cropShape="round"
                        showGrid={false}
                        restrictPosition
                    />
                </div>

                <div className="flex items-center justify-between mt-3">
                    <input
                        type="range"
                        min={1}
                        max={3}
                        step={0.01}
                        value={zoom}
                        onChange={(e) => setZoom(Number(e.target.value))}
                        className="w-2/3"
                    />
                    <div className="flex gap-2">
                        <button onClick={handleCancel} className="px-3 py-1 rounded border">
                            İptal
                        </button>
                        <button
                            onClick={handleDone}
                            disabled={!croppedPixels}
                            className="px-3 py-1 rounded bg-blue-600 text-white disabled:opacity-60"
                        >
                            Kaydet
                        </button>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default AvatarCropper;
