package com.megasoft.entangle;


import org.json.JSONException;
import org.json.JSONObject;
import android.app.Activity;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentActivity;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;
import android.widget.Toast;
import com.megasoft.config.Config;
import com.megasoft.requests.GetRequest;
import com.megasoft.requests.ImageRequest;

/**
 * Views a user's profile given his user Id and the tangle Id that redirected to the profile
 * @author Almgohar
 */
public class ProfileFragment extends Fragment {
	
	/**
	 * The TextView that holds the user's name
	 */
	private TextView name;
	
	/**
	 * The TextView that holds the user's description
	 */
	private TextView description;

	/**
	 * The ImageView that holds the user's profile picture
	 */
	private com.megasoft.entangle.views.RoundedImageView profilePictureView;
	
    
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
	
	/**
	 * The session Id of the logged in user
	 */
	private String sessionId;
	
	/**
	 * The boolean specifying whether the profile is general
	 */
	private boolean isGeneral;
	
	private View view;

	private FragmentActivity activity;
	
	@Override
	public View onCreateView(LayoutInflater inflater, ViewGroup container,
            Bundle savedInstanceState) {
		this.view = inflater.inflate(R.layout.fragment_profile, container,false);	
		this.settings = activity.getSharedPreferences(Config.SETTING, 0);
		this.sessionId = settings.getString(Config.SESSION_ID, "");
		this.loggedInId = settings.getInt(Config.USER_ID, -1);
		this.tangleId = getArguments().getInt("tangleId", 2);
		this.userId = getArguments().getInt("userId", -1);	
		this.isGeneral = getArguments().getBoolean("general");
		viewProfile();		
		return view;
	}

	/**
	 * Initialize all views to link them to the XML views
	 * calls the ViewInformation() method
	 * @author Almgohar
	 */
	public void viewProfile() {
		name = (TextView) view.findViewById(R.id.nameView);
		description = (TextView) view.findViewById(R.id.descriptionView);
		profilePictureView = (com.megasoft.entangle.views.RoundedImageView) view.findViewById(R.id.profileImage);		
		viewInformation();
	}
	
	/**
	 * Creates a JSon request asking for the required information
	 * Retrieves the required information from the JSon response
	 * @author Almgohar
	 */
	public void viewInformation() {
		String link;
		if(isGeneral) {
			link = Config.API_BASE_URL_SERVER + "/user/" + userId + "/profile";

		} else {
			link = Config.API_BASE_URL_SERVER + "/tangle/" + tangleId + "/user/" + userId + "/profile";

		}
		GetRequest request = new GetRequest(link) {
			protected void onPostExecute(String response) {
				if (this.getStatusCode() == 200	) {
				try {
					JSONObject information;
					information = new JSONObject(response);
					name.setText(information.getString("name"));
					description.setText(information.getString("description"));
					viewProfilePicture(information.getString("photo"));
					
					if(activity instanceof ProfileActivity) {
						activity.setTitle(information.getString("name"));
					}
					
					} catch (JSONException e) {
						e.printStackTrace();
						}
				} else {
					Log.e("test", this.getErrorMessage());
					Toast toast = Toast.makeText(activity.getApplicationContext(),"Some error happened.",Toast.LENGTH_SHORT);
					toast.show();
					}
				}
			};
			request.addHeader("X-SESSION-ID", this.sessionId);
			request.execute();
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
	
	@Override
	public void onAttach(Activity activity) {	
	    this.activity = (FragmentActivity) activity;
	    super.onAttach(this.activity);	
	}
}
