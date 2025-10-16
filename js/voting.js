// Voting functionality
document.addEventListener('DOMContentLoaded', function() {
    const voteBtn = document.getElementById('voteBtn');
    
    if (voteBtn) {
        voteBtn.addEventListener('click', function() {
            const ideaId = this.dataset.ideaId;
            
            // Disable button during request
            this.disabled = true;
            
            fetch('php/vote.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: 'idea_id=' + encodeURIComponent(ideaId)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update vote count
                    const voteCount = document.getElementById('voteCount');
                    if (voteCount) {
                        voteCount.textContent = data.voteCount;
                    }
                    
                    // Update button state
                    if (data.userVoted) {
                        this.classList.add('voted');
                        this.nextElementSibling.textContent = 'You voted';
                    } else {
                        this.classList.remove('voted');
                        this.nextElementSibling.textContent = 'Vote for this idea';
                    }
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while voting');
            })
            .finally(() => {
                // Re-enable button
                this.disabled = false;
            });
        });
    }
});

// Quick vote from ideas listing page
function quickVote(ideaId, button) {
    const originalText = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '...';
    
    fetch('php/vote.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'idea_id=' + encodeURIComponent(ideaId)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            button.innerHTML = '👍 ' + data.voteCount;
            if (data.userVoted) {
                button.classList.add('voted');
            } else {
                button.classList.remove('voted');
            }
        } else {
            alert(data.message);
            button.innerHTML = originalText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        button.innerHTML = originalText;
        alert('An error occurred while voting');
    })
    .finally(() => {
        button.disabled = false;
    });
}