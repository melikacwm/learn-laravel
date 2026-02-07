<template>
<div style="padding: 16px" >
    <h1>Notes</h1>

    <!-- untuk search bar-->
     <div style="margin: 16px 0;">
        <input
            v-model="q" 
            type="text"
            placeholder="Search judul..."
            style="padding:8px; width: 320px;"
        />

     </div>

    <form @submit.prevent="submit" style="margin: 16px 0">
        <input
            v-model="form.title"
            type="text"
            placeholder="Judul note..."
            style="padding:8px; width: 320px;"
        />
        <button type="submit" style="margin-left:8px; padding:8px 12px;">
            Tambah
        </button>

        <div v-if="form.errors.title" style="margin-top:8px;">
            {{form.errors.title}}        
        </div>
    </form>
<!-- Isi dari dalam tabel -->
    <ul>
        <li v-for="n in notes.data" :key="n.id" style="margin-bottom:10px;">
            <!-- bagian edit dan hapus-->
            <template v-if="editingId === n.id">
                <input v-model="editForm.title" style="padding: 6px; width: 260px;">
                <button @click="saveEdit(n.id)" style="margin-left: 6px; padding: 4px 8px;">Simpan</button>
                <button @click="cancelEdit" style="margin-left: 6px; padding: 4px 8px;">Batal</button>

                <div v-if="editForm.errors.title" style="margin-top: 6px;">
                    {{ editForm.errors.title }}

                </div>
            </template>

            <template v-else>
                {{ n.title }}
                <button @click="startEdit(n)" style="margin-left: 8px; padding: 4px 8px;">Edit</button>
                <button @click="remove(n.id)" style="margin-left: 6px; padding: 4px 8px;">Hapus</button>
            </template>
            <!-- {{n.title}}
            <button
                @click="remove(n.id)"
                style="margin-left:8px; padding:4px 8px;"
            >
                Hapus
            </button> -->
        </li>
    </ul>
    <!-- bagian pagination -->
     <div style="margin-top: 16px; display: flex; gap:6px; flex-wrap: wrap;">
        <button
            v-for="link in notes.links"
            :key="link.label"
            :disabled="!link.url"
            @click="go(link.url)"
            v-html="link.label"
            style="padding: 6px 10px;"
        ></button>
     </div>
</div>
</template>

<script setup>
import {useForm, router} from '@inertiajs/vue3'
import {ref, watch} from 'vue';

//untuk mendefinisikan bentuk data
const props = defineProps({
    //karena pakai paginator dan filter
    notes: Object, //untuk paginator
    filters: Object, // untuk query

    //untuk yang polos tanpa pagination
    // notes: Array
})

const q = ref(props.filters?.q ?? '')

let t = null
watch(q, (val) => {
    clearTimeout(t)
    t = setTimeout(() => {
        router.get(
            route('notes.index'),
            {q: val},
            {preserveState: true, replace: true}
        )
    }, 400) //debounce 400ms
})

//untuk mendefinisikan form default
const form = useForm({
    title: '',
})

//konstanta untuk mendefinisikan id form yang di edit
const editingId = ref(null)

//konstanta untuk mendefinisikan form editing
const editForm = useForm({
    title: '',
})


//fungsi submit untuk memasukkan data baru
function submit(){
    form.post(route('notes.store'), {
        onSuccess: () => form.reset('title'),
    })
}

//fungsi untuk menghapus data
function remove(id){
    if(!confirm('Yakin hapus note ini?')) return
    form.delete(route('notes.destroy', id))
}

//fungsi untuk mengedit data
function startEdit(note){
    editingId.value = note.id
    editForm.title = note.title
    editForm.clearErrors()
}

//fungsi untuk batal edit
function cancelEdit(){
    editingId.value = null
    editForm.reset()
}

//fungsi untuk menyimpan edit
function saveEdit(id){
    console.log('saveEdit called', id, editForm.title)
    editForm.put(route('notes.update', id), {
        onSuccess: () => cancelEdit(),
        onError: (e) => console.log('error', e)
    })
}

//untuk pagination
function go(url){
    if (!url) return
        router.visit(url, {preserveState: true})
}
</script>