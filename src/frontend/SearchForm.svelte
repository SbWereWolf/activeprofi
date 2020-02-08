<script>
	import { createEventDispatcher } from 'svelte';

	const dispatch = createEventDispatcher();
	let sample = '';
    function search(event) {
        event.preventDefault();
        dispatch('search', {
            sample: sample
        });
    }
</script>

<form class="form-horizontal" id="search"
on:submit={search}>
    <div class="form-group">
        <label class="control-label col-sm-2" for="sample">
        Поиск
        </label>
        <div class="col-sm-10">
            <input id="sample" class="form-control" type="search"
             placeholder="Введите наименование задачи"
             autofocus autocomplete="on"
             bind:value={sample}
             />
        </div>
    </div>
</form>
