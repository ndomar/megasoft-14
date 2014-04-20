package com.megasoft.entangle;



import org.json.JSONException;
import org.json.JSONObject;
import android.app.Activity;
import android.os.Bundle;
import android.util.Log;
import android.widget.Button;
import android.widget.LinearLayout;

import com.megasoft.requests.GetRequest;
import com.megasoft.requests.PostRequest;

public class OfferActivity extends Activity {
	final Activity self = this;
	int offerId = 1;
	JSONObject returnedResponse = null;
	LinearLayout layout;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_main);
		acceptOffer();
	}

	public void acceptOffer() {
		layout = (LinearLayout) this.findViewById(R.id.offer_layout);
		GetRequest request = new GetRequest(
				"http://sak93.apiary-mock.com/offer/" + offerId) {
			protected void onPostExecute(String response) {
				try {
					JSONObject json = new JSONObject(response);
					Log.e("test", response);
					JSONObject offerDetails = (JSONObject) json
							.get("offerInformation");
					JSONObject requestDetails = (JSONObject) json
							.get("requestInformation");
					int requestId = Integer.parseInt(((String) requestDetails
							.get("requestID")));
					int requesterId = Integer.parseInt(((String) requestDetails
							.get("requesterID")));
					int requestStatus = Integer
							.parseInt(((String) requestDetails
									.get("requestStatus")));
					if (requestStatus != 0) {
						return;
					} else {

						int offerStatus = Integer
								.parseInt(((String) offerDetails
										.get("offerStatus")));
						int offererId = Integer.parseInt(((String) offerDetails
								.get("offererID")));
						if (offerStatus == 0) {
							returnedResponse = new JSONObject();
							returnedResponse.put("offerId", ""+ offerId);
							Button button = new Button(self);
							button.setText("Accept offer");
							layout.addView(button);
							PostRequest r = new PostRequest(
									"http://sak93.apiary-mock.com//accept/offer/"
											+ offerId);
							r.setBody(returnedResponse);
							r.addHeader("X-SESSION-ID", "asdasdasdsadasdasd");
							r.execute();
						}
					}
				} catch (JSONException e) {
					e.printStackTrace();
				}
			}

			// private LinearLayout findViewById(Object layout) {
			// // TODO Auto-generated method stub
			// return null;
			// }
		};
		request.addHeader("x-session-id", "asdasdasdsadasdasd");
		request.execute();

		// }
	}
	// public void sendToBackend(){
	// PostRequest response = new
	// PostRequest("http://sak93.apiary-mock.com/offer/" + offerId){
	// public void onPostExecute(){
	//
	// }
	// };
	// }

}
