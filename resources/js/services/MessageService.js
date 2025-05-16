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
    async sendMessage(conversationId, message) {
        try {
            const response = await axios.post(`/api/conversations/${conversationId}/messages`, {
                message // Cambiado de content a message para coincidir con el controlador
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
      // Asegurarnos de que los datos están en el formato correcto
      const payload = {
        recipient_id: data.recipient_id,
        message: data.message,
        job_id: data.job_id !== null ? data.job_id : null
      };
      
      const response = await fetch('/api/conversations', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Accept': 'application/json'
        },
        body: JSON.stringify(payload)
      });
      
      if (!response.ok) {
        // Si hay un error, intentar obtener los detalles
        const errorData = await response.json();
        console.error('Error details:', errorData);
        throw new Error(`Error al crear conversación: ${response.status}`);
      }
      
      return await response.json();
    } catch (error) {
      console.error('Error en createConversation:', error);
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
    async searchAlumni(query = '') {
    try {
      // Hacemos una sola petición para obtener todos los alumni
      const url = `/api/alumni/search?q=${encodeURIComponent(query)}`;
      const response = await fetch(url);
      
      if (!response.ok) {
        throw new Error('Error en la búsqueda de alumni');
      }
      
      return await response.json();
    } catch (error) {
      console.error('Error en searchAlumni:', error);
      // Devolver un array vacío en caso de error
      return { alumni: [] };
    }
  }
}

export default new MessageService();