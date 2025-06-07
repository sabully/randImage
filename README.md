# randImage - 随机返回一张图片
一个基于Flask的Docker容器服务，支持动态子文件夹管理和随机图片展示。当访问/子文件夹时，将随机重定向到该文件夹下的任意图片文件。

## ⭐功能特性
- 动态子文件夹检测
- 支持常见图片格式（PNG/JPG/JPEG/GIF/WEBP）
- 实时文件更新（无需重启服务）
- 随机图片重定向
- 一个服务部署多个API

## ✨环境要求
python3+

## 🚀运行脚本
```python
pip install --no-cache-dir Flask==3.0.2 watchdog==4.0.0 gevent -i https://pypi.tuna.tsinghua.edu.cn/simple --trusted-host pypi.tuna.tsinghua.edu.cn 

## 🏗️使用
部署项目后，无论您使用的是脚本还是容器，请找到映射出的（或目录内的）images文件夹，在里边再次添加子目录（比如pc），访问IP:端口/pc即可在pc这个文件夹内进行随机图片

## 📄 开源协议
本项目采用 MIT 协议开源，详见 [LICENSE](./LICENSE) 文件。

## 🤝赞助
[![Powered by DartNode](https://dartnode.com/branding/DN-Open-Source-sm.png)](https://dartnode.com "Powered by DartNode - Free VPS for Open Source")
