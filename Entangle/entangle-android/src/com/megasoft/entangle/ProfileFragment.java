package com.megasoft.entangle;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import com.megasoft.config.Config;
import com.megasoft.requests.GetRequest;
import com.megasoft.requests.ImageRequest;

import android.app.ActionBar;
import android.app.Activity;
import android.support.v4.app.Fragment;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.support.v4.app.FragmentActivity;
import android.support.v4.view.ViewPager;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.Toast;

public class ProfileFragment extends Fragment {

	/**
	 * The Button that redirects to the EditProfileActivity
	 */
	private Button edit;
	
	/**
	 * The button that allows the user to leave the current tangle
	 */
	private Button leave;
	/**
	 * The TextView that holds the user's name
	 */
	private TextView name;
	
	/**
	 * The TextView that holds the user's description
	 */
	private TextView description;
	
	/**
	 * The TextView that holds the user's credit/balance
	 */
	private TextView balance;
	
	/**
	 * The TextView that holds the user's birth date
	 */
	private TextView birthDate;
	
	/**
	 * The ImageView that indicates whether the user is verified
	 */
	private ImageView verifiedView;
	
	/**
	 * The ImageView that holds the user's profile picture
	 */
	private ImageView profilePictureView;
	
	/**
	 * The LinearLayout that holds the user's transactions
	 */
    private LinearLayout transactionsLayout;
    
    /**
     * The preferences instance
     */
	private SharedPreferences settings;
	
	/**
	 * The id of the logged in user
	 */
	private int loggedInId;
	
	/**
	 * The tangle Id from which we were redirected
	 */
	private int tangleId;
	
	/**
	 * The user Id whose profile we want to view
	 */
	private int userId;
	
	private View view;
	private FragmentActivity activity;
	
	
	@Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        // Inflate the layout for this fragment
		
        view = inflater.inflate(R.layout.activity_profile, container, false);
        this.tangleId = Integer.parseInt(getArguments().getString("key"));
     
		
        viewProfile();
       
       
        
        return view;
    }
	
	@Override
	public void onAttach(Activity activity) {
	    this.activity = (FragmentActivity) activity;
	    super.onAttach(this.activity);
	}
	
	/**
	 * Initialize all views to link them to the XML views
	 * calls the ViewInformation() method
	 * @author Almgohar
	 */
	public void viewProfile() {
		edit = (Button) view.findViewById(R.id.EditProfile);
		leave = (Button) view.findViewById(R.id.LeaveTangle);
		name = (TextView) view.findViewById(R.id.nameView);
		balance = (TextView) view.findViewById(R.id.balanceView);
		birthDate = (TextView) view.findViewById(R.id.birthdateView);
		description = (TextView) view.findViewById(R.id.descriptionView);
		verifiedView = (ImageView) view.findViewById(R.id.verifiedView);
		profilePictureView = (ImageView) view.findViewById(R.id.profileImage);
		transactionsLayout = (LinearLayout) view.findViewById(R.id.transactions_layout);
		
		viewInformation();
	}
	
	/**
	 * Creates a JSon request asking for the required information
	 * Retrieves the required information from the JSon response
	 * @author Almgohar
	 */
	public void viewInformation() {
		String link = "http://entangle2.apiary-mock.com/tangle/" 
	+ tangleId + "/user/" + userId + "/profile";
		GetRequest request = new GetRequest(link) {
			protected void onPostExecute(String response) {
				if (this.getStatusCode() == 200	) {
				try {
					JSONObject jSon;
					jSon = new JSONObject(response);
					JSONArray transactions = jSon.getJSONArray("transactions");
					JSONObject information =  jSon.getJSONObject("information");
					viewTransactions(transactions);
					name.setText(information.getString("name"));
					description.setText("Description: " + information.getString("Description"));
					balance.setText("Credit: " + information.getString("balance") + " points");
					birthDate.setText("Birthdate: " + information.getString("birthdate"));
					viewProfilePicture(information.getString("picture URL"));
					boolean verified = information.getBoolean("verified");
					if (verified) {
							verifiedView.setVisibility(View.VISIBLE);
							} 
					if(loggedInId == userId) {
						edit.setVisibility(View.VISIBLE);
						leave.setVisibility(View.VISIBLE);
						
						edit.setOnClickListener(new View.OnClickListener() {
							@Override
				            public void onClick(View v) {
				            	goToEditProfile();
				            	}
				            });
						leave.setOnClickListener(new View.OnClickListener() {
							@Override
							public void onClick(View v) {
								 leaveTangle();

							}
						});
						}
					} catch (JSONException e) {
						e.printStackTrace();
						}
				} else {
					Toast toast = Toast.makeText(activity.getApplicationContext(),"Some error happened.",Toast.LENGTH_SHORT);
					toast.show();
					}
				}
			};
			request.execute();
	}
	
	/**
	 * Gets the user's transactions from a JSONArray and views them
	 * @param JSONArray transactions
	 * @author Almgohar
	 */
	public void viewTransactions(JSONArray transactions) {
		TextView title = new TextView(activity);
		title.setText("Transactions: ");
		title.setTextSize(20);
		transactionsLayout.addView(title);
		for(int i = 0; i < transactions.length(); i++) {
			JSONObject object;
			try {
				object = transactions.getJSONObject(i);
				TextView transaction = new TextView(activity);
				final int offerId = object.getInt("offerId");
				String requester = object.getString("requesterName");
				String request = object.getString("requestDescription");
				String amount = object.getString("amount");
				transaction.setText("Requester: " + requester 
						+ '\n' + "Request: " + request
						+ '\n' + "Amount: " + amount);
				
				transaction.setOnClickListener(new View.OnClickListener() {
					@Override
					public void onClick(View v) {
						goToOffer(offerId);
						}
				});
				
				transactionsLayout.addView(transaction);
			
				} catch (JSONException e) {
					e.printStackTrace();
					}
			}
		}
	
	/**
	 * Views the user's profile picture
	 * @param String imageURL
	 * @author Almgohar
	 */
	public void viewProfilePicture(String imageURL) {
            ImageRequest image = new ImageRequest(profilePictureView);
            image.execute(imageURL);
	}
	
	/**
	 * Redirects to the EditProfileActivity 
	 * @author Almgohar
	 */
	public void goToEditProfile() {
		Intent editProfile = new Intent(activity, EditProfileActivity.class);
		editProfile.putExtra("user id", loggedInId);
		startActivity(editProfile);
	}
	
	/**
	 * Let the user leave the current tangle
	 * @author Almgohar
	 */
	public void leaveTangle() {
		
	}
	
	/**
	 * Redirects to OfferActivity
	 * @author Almgohar
	 */
	public void goToOffer(int offerId) {
		Intent offer = new Intent(activity,OfferActivity.class);
		offer.putExtra("offer id", offerId);
		startActivity(offer);
	}
	
}
