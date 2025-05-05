function formatLocalDate(timestamp) {
    const date = new Date(timestamp * 1000); // Convert Unix timestamp (seconds) to milliseconds
    return date.toLocaleString(); // Formats date and time in user's local timezone
}