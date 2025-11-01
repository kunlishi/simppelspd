package com.example.presensi.controller;

import com.example.presensi.dto.UserDtos;
import com.example.presensi.model.User;
import com.example.presensi.service.UserService;
import io.swagger.v3.oas.annotations.Operation;
import io.swagger.v3.oas.annotations.tags.Tag;
import jakarta.validation.Valid;
import org.springframework.http.ResponseEntity;
import org.springframework.security.core.Authentication;
import org.springframework.web.bind.annotation.*;

@RestController
@RequestMapping("/api/user")
@Tag(name = "User")
public class UserController {

    private final UserService userService;

    public UserController(UserService userService) {
        this.userService = userService;
    }

    private User currentUser(Authentication authentication) {
        String email = (String) authentication.getPrincipal();
        return userService.getByEmail(email);
    }

    @GetMapping("/me")
    @Operation(summary = "Profil pengguna saat ini")
    public ResponseEntity<UserDtos.ProfileResponse> me(Authentication authentication) {
        User user = currentUser(authentication);
        UserDtos.ProfileResponse resp = new UserDtos.ProfileResponse();
        resp.setId(user.getId());
        resp.setName(user.getName());
        resp.setEmail(user.getEmail());
        resp.setNim(user.getNim());
        resp.setRole(user.getRole().name());
        return ResponseEntity.ok(resp);
    }

    @PutMapping("/me")
    @Operation(summary = "Update profil")
    public ResponseEntity<UserDtos.ProfileResponse> update(Authentication authentication, @Valid @RequestBody UserDtos.UpdateProfileRequest req) {
        User user = currentUser(authentication);
        var updated = userService.updateProfile(user, req);
        UserDtos.ProfileResponse resp = new UserDtos.ProfileResponse();
        resp.setId(updated.getId());
        resp.setName(updated.getName());
        resp.setEmail(updated.getEmail());
        resp.setNim(updated.getNim());
        resp.setRole(updated.getRole().name());
        return ResponseEntity.ok(resp);
    }

    @PostMapping("/change-password")
    @Operation(summary = "Ganti password")
    public ResponseEntity<Void> changePassword(Authentication authentication, @Valid @RequestBody UserDtos.ChangePasswordRequest req) {
        userService.changePassword(currentUser(authentication), req);
        return ResponseEntity.ok().build();
    }

    @DeleteMapping("/me")
    @Operation(summary = "Hapus akun")
    public ResponseEntity<Void> deleteAccount(Authentication authentication) {
        userService.deleteAccount(currentUser(authentication));
        return ResponseEntity.noContent().build();
    }
}
