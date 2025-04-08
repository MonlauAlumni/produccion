import axios from 'axios';

class MessageService {
    /**
     * Obtener todas las conversaciones del usuario
     */
    async getConversations() {
        try {
            const response = await axios.get('/api/conversations');
            return response.data;
        } catch (error) {
            console.error('Error al obtener conversaciones:', error);
            throw error;
        }
    }

    /**
     * Obtener mensajes de una conversación específica
     */
    async getMessages(conversationId, page = 1) {
        try {
            const response = await axios.get(`/api/conversations/${conversationId}/messages?page=${page}`);
            return response.data;
        } catch (error) {
            console.error('Error al obtener mensajes:', error);
            throw error;
        }
    }

    /**
     * Enviar un mensaje en una conversación
     */
    async sendMessage(conversationId, content) {
        try {
            const response = await axios.post(`/api/conversations/${conversationId}/messages`, {
                content
            });
            return response.data;
        } catch (error) {
            console.error('Error al enviar mensaje:', error);
            throw error;
        }
    }

    /**
     * Crear una nueva conversación
     */
    async createConversation(data) {
        try {
            const response = await axios.post('/api/conversations', data);
            return response.data;
        } catch (error) {
            console.error('Error al crear conversación:', error);
            throw error;
        }
    }

    /**
     * Marcar una conversación como leída
     */
    async markAsRead(conversationId) {
        try {
            const response = await axios.post(`/api/conversations/${conversationId}/read`);
            return response.data;
        } catch (error) {
            console.error('Error al marcar como leída:', error);
            throw error;
        }
    }

    /**
     * Obtener trabajos de la empresa (para el modal de nueva conversación)
     */
    async getCompanyJobs() {
        try {
            const response = await axios.get('/api/company/jobs');
            return response.data;
        } catch (error) {
            console.error('Error al obtener trabajos:', error);
            throw error;
        }
    }

    /**
     * Buscar alumni (para el modal de nueva conversación)
     */
    async searchAlumni(query) {
        try {
            const response = await axios.get(`/api/alumni/search?q=${query}`);
            return response.data;
        } catch (error) {
            console.error('Error al buscar alumni:', error);
            throw error;
        }
    }
}

export default new MessageService();