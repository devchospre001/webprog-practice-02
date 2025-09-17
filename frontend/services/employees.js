var EmployeesService = {
  load_employee_performance: function () {
    RestClient.get('/employees/performance', function (response) {
      var tbody = $("#employee-performance tbody");
      tbody.empty();

      response.forEach(function (emp) {
        var tr = `
                    <tr>
                        <td>${emp.id}</td>
                        <td>${emp.full_name}</td>
                        <td>${emp.email}</td>
                        <td>${emp.total}</td>
                        <td>
                            <button class="btn btn-sm btn-primary edit-btn" data-id="${emp.id}">Edit</button>
                            <button class="btn btn-sm btn-danger delete-btn" data-id="${emp.id}">Delete</button>
                        </td>
                    </tr>
                `;

        tbody.append(tr);

        $(".edit-btn").click(function () {
          var id = $(this).data("id");
          EmployeesService.edit_employee(id);
        });

        $(".delete-btn").click(function () {
          var id = $(this).data("id");
          EmployeesService.delete_employee(id);
        });
      })
    });
  },
  delete_employee: function (employee_id) {
    if (
      confirm(
        "Do you want to delete employee with the id " + employee_id + "?"
      ) == true
    ) {
      RestClient.delete('/employee/delete/' + employee_id, function () {
        alert("Employee deleted successfully");
        EmployeesService.load_employee_performance();
      }, function (jqXHR) {
        alert("Error: " + jqXHR.responseText);
      });
    }
  },
  edit_employee: async function (employee_id) {
    RestClient.get('/employee/' + employee_id, function (response) {
      if (response) {
        $("#edit-employee-modal").modal("show");
        $("#edit-employee-modal input[name='employeeNumber']").val(response.employeeNumber);
        $("#edit-employee-modal input[name='firstName']").val(response.firstName);
        $("#edit-employee-modal input[name='lastName']").val(response.lastName);
        $("#edit-employee-modal input[name='email']").val(response.email);
      }
    });
  },
  save_employee: function () {
    const id = $("#edit-employee-modal input[name='employeeNumber']").val();

    const data = {
      firstName: $("#edit-employee-modal input[name='firstName']").val(),
      lastName: $("#edit-employee-modal input[name='lastName']").val(),
      email: $("#edit-employee-modal input[name='email']").val()
    }

    if (!data.firstName || !data.lastName || !data.email) {
      return;
    }

    RestClient.put('/employee/edit/' + id, data, function (response) {
      console.log("Response:", response);

      $("#edit-employee-modal").modal("hide");
      EmployeesService.load_employee_performance();
      toastr.success("Employee updated successfully");
    }, function (jqXHR) {
      toastr.error(jqXHR.responseJSON?.message || "Failed to update employee");
    })
  }
}