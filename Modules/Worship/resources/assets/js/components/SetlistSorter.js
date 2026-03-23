/**
 * SetlistSorter.js
 * Basic drag and drop logic wrapper (using SortableJS or similar if available, or native API).
 * For this implementation, we assume native HTML5 Drag and Drop API for dependencies-free usage.
 */

export class SetlistSorter {
    constructor(containerId, onReorderCallback) {
        this.container = document.getElementById(containerId);
        this.onReorder = onReorderCallback;
        this.draggedItem = null;

        if (this.container) {
            this.init();
        }
    }

    init() {
        const items = this.container.querySelectorAll('[draggable="true"]');

        items.forEach(item => {
            item.addEventListener('dragstart', this.handleDragStart.bind(this));
            item.addEventListener('dragover', this.handleDragOver.bind(this));
            item.addEventListener('drop', this.handleDrop.bind(this));
            item.addEventListener('dragend', this.handleDragEnd.bind(this));
        });
    }

    handleDragStart(e) {
        this.draggedItem = e.target;
        e.dataTransfer.effectAllowed = 'move';
        e.target.style.opacity = '0.5';
    }

    handleDragOver(e) {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
        return false;
    }

    handleDrop(e) {
        e.stopPropagation();
        if (this.draggedItem !== e.target && e.target.parentElement === this.container) {
            // Swap logic could be complex, simplified insertBefore
            // Ideally we find the closest li or div
            const target = e.target.closest('[draggable="true"]');

            if (target) {
                // Get all items to determine direction
                const items = Array.from(this.container.children);
                const draggedIndex = items.indexOf(this.draggedItem);
                const targetIndex = items.indexOf(target);

                if (draggedIndex < targetIndex) {
                    target.after(this.draggedItem);
                } else {
                    target.before(this.draggedItem);
                }

                if (this.onReorder) {
                    this.onReorder();
                }
            }
        }
        return false;
    }

    handleDragEnd(e) {
        this.draggedItem.style.opacity = '1';
        this.draggedItem = null;
    }
}
