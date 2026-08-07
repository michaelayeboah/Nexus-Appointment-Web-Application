    function updateServices() {
                    var doctorSelect = document.getElementById("doctor_type");
                    var serviceSelect = document.getElementById("service_name");
                    var selectedDoctor = doctorSelect.value;

                    var services = {
                        "General Practitioner": ["Standard Consultation", "Routine Checkup", "Blood Work", "Prescription Refill"],
                        "Dentist": ["Dental Consultation", "Teeth Cleaning", "Teeth Whitening", "Cavity Filling", "Tooth Extraction"],
                        "Optometrist": ["Eye Exam", "Contact Lens Fitting", "Glaucoma Screening", "Vision Therapy"],
                        "Pediatrician": ["Child Wellness Exam", "Vaccination", "Sick Visit", "Sports Physical"]
                    };

                    serviceSelect.innerHTML = "";

                    if (selectedDoctor && services[selectedDoctor]) {
                        var options = services[selectedDoctor];
                        for (var i = 0; i < options.length; i++) {
                            var newOption = document.createElement("option");
                            newOption.value = options[i];
                            newOption.text = options[i];
                            serviceSelect.appendChild(newOption);
                        }
                    } else {
                        serviceSelect.innerHTML = '<option value="">-- Please select a specialist first --</option>';
                    }
                }