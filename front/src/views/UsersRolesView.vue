<script setup>
import { onMounted } from 'vue'
import { useUsersStore } from '../stores/users'

const usersStore = useUsersStore()

onMounted(() => {
  usersStore.fetchUsers()
})
</script>

<template>
  <main class="users-roles">
    <p v-if="usersStore.loading" class="status">در حال دریافت اطلاعات...</p>
    <p v-else-if="usersStore.error" class="status error">{{ usersStore.error }}</p>

    <div v-else-if="usersStore.users.length" class="users-list">
      <article v-for="user in usersStore.users" :key="user.id" class="user-card">
        <div class="user-info">
          <strong>{{ user.name }}</strong>
          <span>{{ user.email }}</span>
        </div>

        <div class="roles">
          <span
            v-for="role in user.roles"
            :key="role.id"
            class="role"
            :class="{ inactive: !role.is_active }"
          >
            {{ role.name }}
            <small v-if="!role.is_active">غیرفعال</small>
          </span>
          <span v-if="!user.roles.length" class="empty-role">بدون نقش</span>
        </div>
      </article>
    </div>

    <p v-else-if="usersStore.loaded" class="status">کاربری برای نمایش وجود ندارد.</p>
  </main>
</template>

<style scoped>
.users-roles {
  max-width: 760px;
  margin: 0 auto;
}

.users-list {
  display: grid;
  gap: 12px;
}

.user-card {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  padding: 16px;
  background: #fff;
  border: 1px solid #e2e5e9;
  border-radius: 8px;
}

.user-info {
  display: grid;
  gap: 5px;
}

.user-info span {
  direction: ltr;
  color: #555;
  font-size: 14px;
}

.roles {
  display: flex;
  flex-wrap: wrap;
  justify-content: flex-end;
  gap: 8px;
}

.role {
  padding: 5px 9px;
  border: 1px solid #cfd5dc;
  border-radius: 6px;
  font-size: 13px;
}

.role.inactive {
  opacity: 0.55;
  border-style: dashed;
}

.role small {
  margin-right: 5px;
}

.empty-role,
.status {
  color: #666;
}

.status {
  text-align: center;
}

.status.error {
  color: #b42318;
}

@media (max-width: 600px) {
  .user-card {
    align-items: flex-start;
    flex-direction: column;
  }

  .roles {
    justify-content: flex-start;
  }
}
</style>
