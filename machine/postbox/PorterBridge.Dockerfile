FROM python:3.12-alpine
WORKDIR /bridge
COPY systems/porter/pyproject.toml ./
COPY systems/porter/porter ./porter
RUN pip install --no-cache-dir .
COPY machine/postbox/porter_bridge.py ./porter_bridge.py
CMD ["python", "porter_bridge.py"]
