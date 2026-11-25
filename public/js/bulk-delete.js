document.querySelectorAll('[data-bulk-form]').forEach((form) => {
    const items = () => document.querySelectorAll(`input[form="${form.id}"][name="ids[]"]`);
    const toggleAll = document.querySelector(`[data-bulk-toggle="${form.id}"]`);
    const button = form.querySelector('[data-bulk-submit]');
    const counter = form.querySelector('[data-bulk-count]');

    const checkedCount = () => [...items()].filter((item) => item.checked).length;

    const refresh = () => {
        const total = items().length;
        const checked = checkedCount();

        button.disabled = checked === 0;
        counter.textContent = checked;

        if (toggleAll) {
            toggleAll.checked = total > 0 && checked === total;
            toggleAll.indeterminate = checked > 0 && checked < total;
        }
    };

    toggleAll?.addEventListener('change', () => {
        items().forEach((item) => {
            item.checked = toggleAll.checked;
        });
        refresh();
    });

    items().forEach((item) => item.addEventListener('change', refresh));

    form.addEventListener('submit', (event) => {
        if (!confirm(`Excluir ${checkedCount()} item(ns) selecionado(s)? Esta ação não pode ser desfeita.`)) {
            event.preventDefault();
        }
    });

    refresh();
});
