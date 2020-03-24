const getContacto = (state) => (id) => {
    return state.contactos.find(contacto => contacto.id === id)
}

export {
    getContacto,
}