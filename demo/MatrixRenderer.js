
class MatrixRenderer
{
    _matrix;
    _container;

    constructor(matrix) {
        this._matrix = matrix;
    }

    render(target) {
        this._container = target;

        for(let line of this._matrix) {
            this.renderLine(line);
        }
    }

    renderLine(line) {
        let row = document.createElement('div');
        row.classList.add('row');

        for(let pixel of line) {
            let element = document.createElement('div');
            element.classList.add('pixel');
            element.classList.add('pixel-' + pixel);
            row.appendChild(element);

        }

        this._container.appendChild(row);
    }
}