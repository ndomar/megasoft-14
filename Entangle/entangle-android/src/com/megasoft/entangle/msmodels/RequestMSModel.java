package com.megasoft.entangle.msmodels;

import java.util.ArrayList;
import java.util.List;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import android.util.Log;

import com.megasoft.entangle.msmodels.interfaces.DoneCallback;
import com.megasoft.entangle.msmodels.interfaces.FailCallback;
import com.megasoft.entangle.msmodels.interfaces.MSModel;
import com.megasoft.requests.PostRequest;
import com.megasoft.requests.PutRequest;

public class RequestMSModel implements MSModel{
	
	public void how_to_use_this_model() {
		final RequestMSModel model = new RequestMSModel("/tangle/123/request", "oba7");
		model.setTitle("Allah");
		model.setDescription("walla3 walla3");
		model.setRequestedPrice(100);
		model.addTag("guc");
		Promise p = model.save();
		// do this if everything wentOk (optional)
		p.done(new DoneCallback() {
			
			@Override
			public void onDone(String response) {
				Log.e("walla3", " " + model.getId());
				
			}
		//do this if there was a problem (optional)
		}).fail(new FailCallback() {
			
			@Override
			public void onFail(Object response) {
				
				
			}
		});
	}
	
	
	private String resource;
	private int id = -1;
	private String title;
	private String description;
	private List<String> tags;
	private int requestedPrice;
	private String sessionId;
	private int userId;
	private int count;
	
	
	public RequestMSModel(String resource, String sessionId) {
		this.resource = resource;
		this.sessionId = sessionId;
		this.tags = new ArrayList<String>();
	}
	
	
	public String getResource() {
		return resource;
	}

	public void setResource(String resource) {
		this.resource = resource;
	}


	public int getId() {
		return id;
	}

	public String getTitle() {
		return title;
	}

	public void setTitle(String title) {
		this.title = title;
	}

	public String getDescription() {
		return description;
	}

	public void setDescription(String description) {
		this.description = description;
	}

	public List<String> getTags() {
		return tags;
	}

	public void addTag(String tag) {
		this.tags.add(tag);
	}


	public int getRequestedPrice() {
		return requestedPrice;
	}

	public void setRequestedPrice(int requestedPrice) {
		this.requestedPrice = requestedPrice;
	}	
	
	protected void setUserId(int userId) {
		this.userId = userId;
	}
	
	public int getUserId() {
		return userId;
	}
	
	public Promise acceptOffer(int offerId) {
		final Promise promise = new Promise();
			PostRequest request = new PostRequest(MSModel.rootResource + resource + "/accept/" + offerId) {
				
				protected void onPostExecute(String res) {
	
					if (this.getStatusCode() >= 200 && this.getStatusCode() < 300){
							promise.resolve("Created");
					} else { 
						promise.reject(this.getErrorMessage());
					}
				}
			};
			request.addHeader("X-SESSION-ID", sessionId);
			request.execute();
		return promise;
	}
	
	protected void setId(int id) {
		this.id = id;
	}
	
	protected void setOffersCount(int count) {
		this.count = count;
	}
	
	public int getOffersCount() {
		return count;
	}
	
	@Override
	public Promise save() {
		final Promise promise = new Promise();
		JSONObject json = new JSONObject();
		try {
			json.put("title", getTitle());
			json.put("description", getTitle());
			json.put("tags", new JSONArray(getTags()));
			json.put("requestedPrice", getRequestedPrice());
		} catch (JSONException e) {
			e.printStackTrace();
		}
		
		if (id == -1) {
			PostRequest request = new PostRequest(MSModel.rootResource + resource) {
				
				protected void onPostExecute(String res) {
	
					if (this.getStatusCode() >= 200 && this.getStatusCode() < 300){
						try {
							JSONObject jsonResponse = new JSONObject(res);
							id = jsonResponse.getInt("id");
							//update the resource of the object
							setResource(getResource() + "/" + id);
							
							promise.resolve("Created");
						} catch (JSONException e) {
							e.printStackTrace();
						}
					}else { 
						promise.reject(this.getErrorMessage());
					}
				}
			};
			request.setBody(json);
			request.addHeader("X-SESSION-ID", sessionId);
			request.execute();

		} else {
			PutRequest request = new PutRequest(MSModel.rootResource + resource) {
				
				protected void onPostExecute(String res) {
	
					if (this.getStatusCode() >= 200 && this.getStatusCode() < 300){
							promise.resolve("Done!");
					} else { 
						promise.reject(this.getErrorMessage());
					}
				}
			};
			request.setBody(json);
			request.addHeader("X-SESSION-ID", sessionId);
			request.execute();
		}
		
		return promise;
	}

	@Override
	public Promise fetch() {
		// TODO Auto-generated method stub
		return null;
	}

}
