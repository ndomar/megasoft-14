package com.example.first;

import java.util.Date;

public class User {
	public int id;
	public String name;
	public String primaryEmail;
	public String[] emails;
	private String password;
	public String bio;
	public Date birthDate;
	private int age;
	private boolean verified;
	private boolean emailNotifications;

	public User(int id, String name, String primaryEmail, String[] emails,
			String password, String bio, Date birthDate, int age,
			boolean verified, boolean emailNotifications) {
		this.id = id;
		this.name = name;
		this.primaryEmail = primaryEmail;
		this.emails = emails;
		this.password = password;
		this.bio = bio;
		this.birthDate = birthDate;
		this.age = age;
		this.verified = verified;
		this.emailNotifications = emailNotifications;
	}
	
	public void setName(String name){
		this.name = name;
	}
	
	public String getName(){
		return name;
	}
	
	public void setEmailNotifications(boolean value){
		this.emailNotifications = value;
	}
}
