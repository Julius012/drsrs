document.getElementById('userType').addEventListener('change', function () {
    if (this.value === 'attachee') {
        document.getElementById('institutionField').style.display = 'block';
        document.getElementById('academicYearField').style.display = 'block';
    } else {
        document.getElementById('institutionField').style.display = 'none';
        document.getElementById('academicYearField').style.display = 'none';
    }
});

function register() {
    const userType = document.getElementById('userType').value;
    const fullName = document.getElementById('fullName').value;
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    let institution = '';
    let academicYear = '';

    if (userType === 'attachee') {
        institution = document.getElementById('institution').value;
        academicYear = document.getElementById('academicYear').value;
    }

    if (password !== confirmPassword) {
        alert('Passwords do not match!');
        return;
    }

    // Here you would typically send the data to the server
    console.log('User registered:', {
        userType,
        fullName,
        email,
        password,
        institution,
        academicYear
    });

    alert('Registration successful!');
    document.getElementById('registerForm').reset();
}

function login() {
    const email = document.getElementById('loginEmail').value;
    const password = document.getElementById('loginPassword').value;

    // Here you would typically validate the login credentials with the server
    console.log('User logged in:', { email, password });

    alert('Login successful!');

    // Simulate attachee login
    if (email === 'attachee@example.com') {
        document.getElementById('reportUpload').style.display = 'block';
    }
}

function uploadReport() {
    const reportType = document.getElementById('reportType').value;
    const reportFile = document.getElementById('reportFile').files[0];

    // Here you would typically upload the file to the server
    console.log('Report uploaded:', { reportType, reportFile });

    alert('Report uploaded successfully!');
    document.getElementById('reportForm').reset();
}
