<template>
  <div v-if="visible" class="departments-modal-overlay">
    <div class="departments-modal">
      <button class="departments-modal-close" @click="closeModal">&times;</button>
      <h3 class="departments-modal-title">Navegar por Departamentos</h3>
      <div v-if="loading" class="departments-loading">Carregando departamentos...</div>
      <div v-else>
        <div v-if="departments.length === 0" class="text-muted">Nenhum departamento encontrado.</div>
        <ul class="departments-list">
          <li v-for="dept in departments" :key="dept.id" class="department-item">
            <a :href="`/departamento/${dept.slug}`" class="department-link">
              <strong>{{ dept.name }}</strong>
              <span class="text-muted">({{ dept.slug }})</span>
            </a>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      departments: [],
      loading: true,
      visible: false,
    };
  },
  mounted() {
    this.fetchDepartments();
    window.addEventListener('showDepartmentsModal', this.openModal);
  },
  beforeUnmount() {
    window.removeEventListener('showDepartmentsModal', this.openModal);
  },
  methods: {
    fetchDepartments() {
      this.loading = true;
      fetch('/admin/departments/json')
        .then(response => response.json())
        .then(data => {
          this.departments = data;
          this.loading = false;
        })
        .catch(() => {
          this.loading = false;
          this.departments = [];
        });
    },
    openModal() {
      this.visible = true;
    },
    closeModal() {
      this.visible = false;
    },
  },
};
</script>

<style>
/* Modal overlay styles */
.departments-modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background: rgba(0,0,0,0.4);
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
}
.departments-modal {
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 4px 32px rgba(0,0,0,0.18);
  padding: 2rem 2.5rem;
  min-width: 320px;
  max-width: 90vw;
  max-height: 80vh;
  overflow-y: auto;
  position: relative;
}
.departments-modal-close {
  position: absolute;
  top: 12px;
  right: 16px;
  background: none;
  border: none;
  font-size: 2rem;
  color: #888;
  cursor: pointer;
}
.departments-modal-title {
  margin-bottom: 1.5rem;
  font-size: 1.3rem;
  font-weight: 600;
  color: #333;
}
.departments-list {
  margin-top: 1rem;
  padding: 0;
  list-style: none;
}
.department-item {
  padding: 0.5rem 0;
  border-bottom: 1px solid #eee;
}
.department-link {
  text-decoration: none;
  color: #333;
  transition: color 0.2s;
}
.department-link:hover {
  color: #007bff;
}
</style>
