import os
from PIL import Image
from pathlib import Path

def convert_folder_to_webp(
    input_folder: str,
    output_folder: str,
    quality: int = 80,
    delete_original: bool = False,
    verbose: bool = True
    ) -> None:
    """
    批量转换文件夹中的图像为 WebP 格式
    :param input_folder: 输入文件夹路径
    :param output_folder: 输出文件夹路径（自动创建）
    :param quality: WebP 质量 (1-100)
    :param delete_original: 是否删除原始文件
    :param verbose: 是否打印进度
    """
    # 创建输出目录
    Path(output_folder).mkdir(parents=True, exist_ok=True)
    
    supported_formats = (".jpg", ".jpeg", ".png", ".bmp", ".tiff")
    converted_count = 0
    skipped_files = []
    error_files = []

    for filename in os.listdir(input_folder):
        print(f"Processing: {filename}")
        input_path = os.path.join(input_folder, filename)
        
        # 跳过子目录和非图像文件
        if not os.path.isfile(input_path):
            continue
        if not filename.lower().endswith(supported_formats):
            skipped_files.append(filename)
            continue

        # 构建输出路径
        stem = Path(filename).stem
        output_path = os.path.join(output_folder, f"{stem}.webp")

        try:
            with Image.open(input_path) as img:
                # 转换模式兼容性处理
                if img.mode not in ["RGB", "RGBA"]:
                    img = img.convert("RGB")
                
                # 保存为 WebP
                save_args = {'quality': quality}
                if img.mode == "RGBA":
                    save_args['lossless'] = False  # WebP 对透明通道的特殊处理
                
                img.save(output_path, "WEBP", **save_args)
                converted_count += 1

                # 可选：删除原始文件
                if delete_original:
                    os.remove(input_path)

                if verbose:
                    print(f"✅ Converted: {filename} -> {stem}.webp")

        except Exception as e:
            error_files.append(filename)
            if verbose:
                print(f"❌ Failed to convert {filename}: {str(e)}")

    # 打印统计信息
    print(f"\nConversion complete!")
    print(f"✅ Successfully converted: {converted_count} files")
    print(f"⏭️ Skipped non-image files: {len(skipped_files)}")
    print(f"❌ Failed conversions: {len(error_files)}")
    if error_files and verbose:
        print("\nFailed files:")
        for f in error_files:
            print(f" - {f}")

if __name__ == "__main__":
    # 使用示例
    input_dir =  r"C:\Users\98424\Desktop\2025_250606_222401\2025\05\30"   # 替换为你的输入目录
    output_dir = r"C:\Users\98424\Desktop\lalala"  # 替换为你的输出目录
    
    convert_folder_to_webp(
        input_folder=input_dir,
        output_folder=output_dir,
        quality=70,          # 推荐 80-90 之间的质量
        delete_original=False,  # 谨慎使用！
        verbose=True
    )