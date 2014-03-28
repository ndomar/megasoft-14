package com.megasoft.entangle.msmodels;

import org.json.JSONException;
import org.json.JSONObject;

import com.megasoft.entangle.exceptions.InvalidPasswordException;
import com.megasoft.entangle.msmodels.interfaces.MSModel;
import com.megasoft.entangle.msmodels.interfaces.User;
import com.megasoft.requests.PostRequest;

public class UserMSModel implements User {
	
	private String username;
	private String password;
	private int id;
	private String resource;
	
	public UserMSModel(String resource) {
		this.resource = resource;
	}
	

	@Override
	public Promise save() {
		final Promise promise = new Promise();
		PostRequest request = new PostRequest(MSModel.rootResource + resource) {
			protected void onPostExecute(String response) {
				if (this.getStatusCode() >= 200 && this.getStatusCode() < 300){
					JSONObject json;
					try {
						json = new JSONObject(response);
						id = Integer.parseInt(json.getString("id"));
						promise.resolve("Ok");
					} catch (JSONException e) {
						e.printStackTrace();
					}
                    ;
				}else { 
					promise.reject(this.getErrorMessage());
				}
			}
		};
		JSONObject json = new JSONObject();
		try {
			json.put("username", username);
			json.put("password", password);
		} catch (JSONException e) {
			e.printStackTrace();
		}
		request.setBody(json);
		request.execute();		
		return promise;
	}

	@Override
	public Promise fetch() {
		// TODO Auto-generated method stub
		return null;
	}

	@Override
	public int getUserID() {
		return id;
	}

	@Override
	public void setUsername(String username) {
		this.username = username;

	}

	@Override
	public String getUsername() {
		return this.username;
	}

	@Override
	public void setPassword() throws InvalidPasswordException {
		this.password = password;

	}

}
